<?php

namespace DrSlump\Protobuf\Codec\Binary;

use DrSlump\Protobuf;
use DrSlump\Protobuf\Codec\PhpArray;


// Extends Binary/Native to use it's encoding mechanism. Only decoding
// is performed with the help of the C extension at this point.
class Extension extends Native
{
    protected $_resources = array();
    protected $_codec = NULL;

    /**
     * @param String|\DrSlump\Protobuf\MessageInterface $message
     * @param String $data
     * @return \DrSlump\Protobuf\Message
     */
    public function decode(Protobuf\MessageInterface $message, $data)
    {
        return $this->_decodeMessage($message, $data);
    }

    /**
     * Decodes the given message returning a plain array. This is not part
     * of the official API for codecs but could be useful in some cases 
     * were it's needed maximum performance.
     *
     * @param String|\DrSlump\Protobuf\MessageInterface $message
     * @param String $data
     * @return Array
     */
    public function decodeAsArray(Protobuf\MessageInterface $message, $data)
    {
        $descriptor = Protobuf\Protobuf::getRegistry()->getDescriptor($message);
        $name = $descriptor->getName();

        $res = $this->_describe($descriptor);
        return \protobuf_decode($res, $data);
    }



    protected function _describe(Protobuf\Descriptor $descriptor)
    {
        $name = $descriptor->getName();
        if (isset($this->_resources[$name])) {
            return $this->_resources[$name];
        }

        // Create the message descriptor resource
        $res = \protobuf_desc_message($name);
    
        // Add the resource immediately in the dictionary to support cyclic references
        $this->_resources[$name] = $res;

        $lazy = $this->getOption('lazy');

        // Iterate over all the fields to setup the message
        foreach ($descriptor->getFields() as $field) {

            $type = $field->getType();

            // Nested messages need to be populated first
            if ($type === Protobuf\Protobuf::TYPE_MESSAGE) {
                // When in lazy decoding mode we handle nested messages as binary fields
                if ($lazy) {
                    $type = Protobuf\Protobuf::TYPE_BYTES;
                } else {
                    // Try to obtain the message descriptor resource for this field
                    $descr = Protobuf\Protobuf::getRegistry()->getDescriptor($field->getReference());
                    if (!$descr) {
                        throw new \RuntimeException('Unable to find a descriptor for message "' . $field->getReference() . '"');
                    }
                    $nested = $this->_describe($descr);
                }
            }

            //printf("N: %d R: %d T: %d Name: %s P: %d\n", $field->getNumber(), $field->getRule(), $type, $field->getName(), $field->isPacked() ? PROTOBUF_FLAG_PACKED : 0);

            // Append the field definition to the message
            \protobuf_desc_field(
                $res, 
                $field->getNumber(),
                $field->getRule(),
                $type,
                $field->getName(),
                $field->isPacked() ? PROTOBUF_FLAG_PACKED : 0,
                $type === Protobuf\Protobuf::TYPE_MESSAGE ? $nested : NULL
            );
        }

        return $res;
    }

    /**
     * @param \DrSlump\Protobuf\MessageInterface    $message
     * @param string                                $data
     * @return \DrSlump\Protobuf\MessageInterface
     */
    protected function _decodeMessage(\DrSlump\Protobuf\MessageInterface $message, $data)
    {
        $descriptor = Protobuf\Protobuf::getRegistry()->getDescriptor($message);
        $name = $descriptor->getName();

        $res = $this->_describe($descriptor);
        $ret = \protobuf_decode($res, $data);

        // In non lazy mode we just pass the returned array thru the PhpArray codec
        if (!$this->getOption('lazy')) {
            if (!$this->_codec) {
                $this->_codec = new PhpArray();
                $this->_codec->setOption('lazy', false);
            }

            return $this->_codec->decode($message, $ret);
        }

 
        // In lazy mode we need to walk thru the fields to convert message strings
        // to LazyValue / LazyRepeat

        foreach ($descriptor->getFields() as $field) {
            $name = $field->getName();
            if (!isset($ret[$name])) continue;

            $value = $ret[$name];

            if ($field->getType() === Protobuf\Protobuf::TYPE_MESSAGE) {
                if ($field->getRule() === Protobuf\Protobuf::RULE_REPEATED) {
                    $value = new Protobuf\LazyRepeat($value);
                    $value->codec = $this;
                    $value->descriptor = $field;
                } else {
                    $lazy = new Protobuf\LazyValue();
                    $lazy->codec = $this;
                    $lazy->descriptor = $field;
                    $lazy->value = $value;
                    $value = $lazy;
                }
            }

            $message->initValue($name, $value);
        }

        return $message;
    }

    public function lazyDecode($field, $bytes)
    {
        if ($field->getType() === Protobuf\Protobuf::TYPE_MESSAGE) {
            $msg = $field->getReference();
            $msg = new $msg;
            return $this->_decodeMessage($msg, $bytes);
        }

        throw new \RuntimeException('Only message types are supported to be decoded lazily');
    }
}
