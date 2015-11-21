<?php

namespace Box\Brainy\Compiler\Constructs;


class ConstructBlockNonterminal extends ClosedBaseConstruct
{
    /**
     * @param  \Box\Brainy\Compiler\TemplateCompiler $compiler A compiler reference
     * @param  array|null  $args     Arguments
     * @return mixed
     */
    public static function compileOpen(\Box\Brainy\Compiler\TemplateCompiler $compiler, $args)
    {
        $name = self::getName($compiler, $args);
        $forced = self::getOptionalArg($args, 'force');

        $childBlockVar = '$' . $compiler->getUniqueVarName();

        self::openTag($compiler, 'block', array(
            'name' => $name,
            'childVar' => $childBlockVar,
            'forced' => $forced,
        ));

        if ($forced) {
            return self::compileForced($compiler, $childBlockVar);
        }

        $nameVar = '$' . $compiler->getUniqueVarName();

        $output = "if (!array_key_exists('blocks', \$_smarty_tpl->tpl_vars['smarty']->value)) {\n";
        $output .= "  \$_smarty_tpl->tpl_vars['smarty']->value['blocks'] = array();\n";
        $output .= "}\n";
        $output .= "$nameVar = $name;\n";
        $output .= "if (!array_key_exists($nameVar, \$_smarty_tpl->tpl_vars['smarty']->value['blocks'])) {\n";
        $output .= "  $childBlockVar = null;\n";
        $output .= "  \$_smarty_tpl->tpl_vars['smarty']->value['blocks'][$nameVar] = function (\$_smarty_tpl) use ($childBlockVar) {\n";
        return $output;
    }

    /**
     * @param  \Box\Brainy\Compiler\TemplateCompiler $compiler A compiler reference
     * @param  array|null  $args     Arguments
     * @return mixed
     */
    public static function compileClose(\Box\Brainy\Compiler\TemplateCompiler $compiler, $args)
    {
        $data = self::closeTag($compiler, 'block');
        if ($data['forced']) {
            return "};\n";
        }
        return "  };\n}\n";
    }

    /**
     * @param  \Box\Brainy\Compiler\TemplateCompiler $compiler A compiler reference
     * @param  array|null  $args     Arguments
     * @return mixed
     */
    protected static function getName($compiler, $args)
    {
        try {
            return self::getRequiredArg($args, 'name');
        } catch (\Box\Brainy\Exceptions\SmartyCompilerException $e) {
            $compiler->assert_is_not_strict('Block shorthand is not allowed in strict mode. Use the name="" attribute instead.');
            if (!isset($args[0])) {
                throw $e;
            }
            return $args[0];
        }
    }

    /**
     * @param  \Box\Brainy\Compiler\TemplateCompiler $compiler
     * @param  string $childBlockVar
     * @return string
     */
    protected static function compileForced($compiler, $childBlockVar)
    {
        $output = "if (!array_key_exists('blocks', \$_smarty_tpl->tpl_vars['smarty']->value)) {\n";
        $output .= "  \$_smarty_tpl->tpl_vars['smarty']->value['blocks'] = array();\n";
        $output .= "  $childBlockVar = null;\n";
        $output .= "} else $childBlockVar = \$_smarty_tpl->tpl_vars['smarty']->value['blocks'][$nameVar] ?: null;\n";
        $output .= "\$_smarty_tpl->tpl_vars['smarty']->value['blocks'][$nameVar] = function (\$_smarty_tpl) use ($childBlockVar) {\n";
        return $output;
    }

}