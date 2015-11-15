<?php

namespace Box\Brainy\Compiler\Helpers;


class Tag extends ParseTree
{

    /**
     * Create parse tree buffer for Smarty tag
     *
     * @param object $parser parser object
     * @param string $data   content
     */
    public function __construct($data) {
        $this->data = $data;
    }

    /**
     * @return string
     */
    public function to_inline_data() {
        throw new \Box\Brainy\Exceptions\SmartyException('Brainy tag cast to inline template data');
    }

    /**
     * Return buffer content
     *
     * @return string content
     */
    public function to_smarty_php() {
        return $this->data;
    }

    /**
     * @return bool
     */
    public function can_combine_inline_data() {
        return false;
    }

}