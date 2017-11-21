<?php

namespace DrSlump\Protobuf\Codec;

use DrSlump\Protobuf;

/**
 * This codec serializes and unserializes from/to Json strings
 * where the keys are packed as the first element of numeric arrays,
 * optimizing the resulting payload size.
 *
 */
class JsonIndexed extends Json
    implements Protobuf\CodecInterface
{

    protected function encodeMessage(Protobuf\Message $message)
    {
        $descriptor = Protobuf\Protobuf::getRegistry()->getDescriptor($message);

        $strict = $this->getOption('strict');

        $index = '';
        $data = array();

        foreach ($descriptor->getFields() as $tag=>$field) {

            $empty = !isset($message[$tag]);
            if ($strict && $empty && $field->isRequired() && !$field->hasDefault()) {
                throw new \UnexpectedValueException(
                    'Message ' . get_class($message) . '\'s field tag ' . $tag . '(' . $field->getName() . ') is required but has no value'
                );
            }

            // Skip unknown
            if ($empty && !$field->hasDefault()) {
                continue;
            }

            $value = $message[$tag];
            if ($value === NULL) {
                continue;
            }

            // Add the item to the index
            $index .= $this->i2c($tag + 48);

            if ($field->isRepeated()) {
                $repeats = array();
                foreach ($value as $val) {
                    if ($field->getType() !== Protobuf\Protobuf::TYPE_MESSAGE) {
                        $repeats[] = $val;
                    } else {
                        $repeats[] = $this->encodeMessage($val);
                    }
                }
                $data[] = $repeats;
            } else {
                if ($field->getType() === Protobuf\Protobuf::TYPE_MESSAGE) {
                    $data[] = $this->encodeMessage($value);
                } else {
                    $data[] = $value;
                }
            }
        }

        // Insert the index at first element
        array_unshift($data, $index);

        return $data;
    }

    protected function decodeMessage(Protobuf\Message $message, $data)
    {
        // Get message descriptor
        $descriptor = Protobuf\Protobuf::getRegistry()->getDescriptor($message);

        // Split the index in UTF8 characters
        preg_match_all('/./u', $data[0], $chars);

        $chars = $chars[0];
        for ($i=1; $i<count($data); $i++) {

            $k = $this->c2i($chars[$i-1]) - 48;
            $v = $data[$i];

            $field = $descriptor->getField($k);

            if (NULL === $field) {
                // Unknown
                $unknown = new PhpArray\Unknown($k, gettype($v), $v);
                $message->addUnknown($unknown);
                continue;
            }

            $name = $field->getName();

            if ($field->getType() === Protobuf\Protobuf::TYPE_MESSAGE) {
                $nested = $field->getReference();
                if ($field->isRepeated()) {
                    foreach ($v as $kk=>$vv) {
                        $v[$kk] = $this->decodeMessage(new $nested, $vv);
                    }
                    $message->initValue($name, $v);
                } else {
                    $obj = $this->decodeMessage(new $nested, $v);
                    $message->initValue($name, $obj);
                }
            } else {
                $message->initValue($name, $v);
            }
        }

        return $message;
    }

    /**
     * Converts an Unicode codepoint number to an UTF-8 character
     *
     * @param int $codepoint
     * @return string
     */
    protected function i2c($codepoint)
    {
        return $codepoint < 128
               ? chr($codepoint)
               : html_entity_decode("&#$codepoint;", ENT_NOQUOTES, 'UTF-8');
    }

    /**
     * Converts an UTF-8 character to an Unicode codepoint number
     *
     * @param string $char
     * @return int
     */
    protected function c2i($char)
    {
        $value = ord($char[0]);
        if ($value < 128) return $value;

        if ($value < 224) {
            return (($value % 32) * 64) + (ord($char[1]) % 64);
        } else {
            return (($value % 16) * 4096) +
                   ((ord($char[1]) % 64) * 64) +
                   (ord($char[2]) % 64);
        }
    }

}
