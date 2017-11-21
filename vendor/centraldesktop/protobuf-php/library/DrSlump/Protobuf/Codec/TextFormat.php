<?php

namespace DrSlump\Protobuf\Codec;

use DrSlump\Protobuf;

/**
 * This codec serializes to Protobuf's TextFormat, unserialization
 * is not supported.
 *
 */
class TextFormat extends Protobuf\CodecAbstract
    implements Protobuf\CodecInterface
{
    /**
     * @param \DrSlump\Protobuf\MessageInterface $message
     * @return string
     */
    public function encode(Protobuf\MessageInterface $message)
    {
        return $this->encodeMessage($message);
    }

    /**
     *
     * @throw \DrSlump\Protobuf\Exception - Decoding is not supported
     * @param \DrSlump\Protobuf\MessageInterface $message
     * @param String $data
     * @return \DrSlump\Protobuf\MessageInterface
     */
    public function decode(Protobuf\MessageInterface $message, $data)
    {
        throw new \BadMethodCallException('TextFormat codec does not support decoding');
    }


    protected function encodeMessage(Protobuf\MessageInterface $message, $level = 0)
    {
        $descriptor = Protobuf\Protobuf::getRegistry()->getDescriptor($message);

        $strict = $this->getOption('strict');

        $indent = str_repeat('  ', $level);
        $data = '';
        foreach ($descriptor->getFields() as $tag=>$field) {

            $empty = !isset($message[$tag]);
            if ($strict && $empty && $field->isRequired() && !$field->hasDefault()) {
                throw new \UnexpectedValueException(
                    'Message ' . $descriptor->getName() . '\'s field tag ' . $tag . '(' . $field->getName() . ') is required but has no value'
                );
            }

            if ($empty && !$field->hasDefault()) {
                continue;
            }

            $name = $field->getName();
            $value = $message[$tag];

            if ($value === NULL) {
                continue;
            }

            if ($field->isRepeated()) {
                foreach ($value as $val) {
                    // Skip nullified repeated values
                    if (NULL === $val) {
                        continue;
                    } else if ($field->getType() !== Protobuf\Protobuf::TYPE_MESSAGE) {
                        $data .= $indent . $name . ': ' . json_encode($val) . "\n";
                    } else {
                        $data .= $indent . $name . " {\n";
                        $data .= $this->encodeMessage($val, $level+1);
                        $data .= $indent . "}\n";
                    }
                }
            } else {
                if ($field->getType() === Protobuf\Protobuf::TYPE_MESSAGE) {
                    $data .= $indent . $name . " {\n";
                    $data .= $this->encodeMessage($value, $level+1);
                    $data .= $indent . "}\n";
                } else {
                    $data .= $indent . $name . ': ' . json_encode($value) . "\n";
                }
            }
        }

        return $data;
    }
}

