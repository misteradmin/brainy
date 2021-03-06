<?php

namespace Box\Brainy\Compiler\Constructs;

use \Box\Brainy\Brainy;

class ConstructFor extends ClosedBaseConstruct
{
    /**
     * Compiles the opening tag for a function
     * @param  \Box\Brainy\Compiler\TemplateCompiler $compiler A compiler reference
     * @param  array|null                            $args     Arguments
     * @return mixed
     */
    public static function compileOpen(\Box\Brainy\Compiler\TemplateCompiler $compiler, $args)
    {
        if (self::hasArg($args, 'ifexp')) {
            return self::compileOpenCStyle($compiler, $args);
        } else {
            return self::compileOpenShorthand($compiler, $args);
        }
    }

    /**
     * @param  \Box\Brainy\Compiler\TemplateCompiler $compiler A compiler reference
     * @param  array|null                            $args     Arguments
     * @return mixed
     */
    public static function compileOpenCStyle(\Box\Brainy\Compiler\TemplateCompiler $compiler, $args)
    {
        $start = self::getRequiredArg($args, 'start');
        $ifexp = self::getRequiredArg($args, 'ifexp');
        $var = self::getRequiredArg($args, 'var');
        $step = self::getRequiredArg($args, 'step');

        $output = '';
        foreach ($start as $stmt) {
            $output .= "\$_smarty_tpl->setVariable({$stmt['var']}, {$stmt['value']});\n";
        }
        $output .= "if ($ifexp) {\n";
        $output .= "  for (\$_foo=true; {$ifexp}; \$_smarty_tpl->tpl_vars[{$var}]{$step}) {\n";

        self::openTag($compiler, 'for');

        return $output;
    }

    /**
     * @param  \Box\Brainy\Compiler\TemplateCompiler $compiler A compiler reference
     * @param  array|null                            $args     Arguments
     * @return mixed
     */
    public static function compileOpenShorthand(\Box\Brainy\Compiler\TemplateCompiler $compiler, $args)
    {
        $start = self::getRequiredArg($args, 'start');
        $to = self::getRequiredArg($args, 'to');
        $step = self::getOptionalArg($args, 'step', 1);
        $max = self::getOptionalArg($args, 'max', INF);

        $var = $start['var'];
        $value = $start['value'];

        $total = "ceil(($step > 0 ? $to + 1 - ($value) : $value - ($to) + 1) / abs($step))";
        if ($max !== INF) {
            $total = "min($total, $max)";
        }

        $stepVar = '$' . $compiler->getUniqueVarName();
        $totalVar = '$' . $compiler->getUniqueVarName();
        $iterationVar = '$' . $compiler->getUniqueVarName();

        $output = "$stepVar = $step;\n";
        $output .= "$totalVar = (int) $total;\n";

        $output .= "if ($totalVar > 0) {\n";
        $output .= "  \$_smarty_tpl->setVariable($var, 0);\n";

        $varVar = "\$_smarty_tpl->tpl_vars[$var]";
        $output .= "  for ($varVar = $value, $iterationVar = 1; $iterationVar <= $totalVar; $varVar += $stepVar, $iterationVar++) {\n";

        self::openTag($compiler, 'for', array('for'));

        return $output;
    }

    /**
     * Compiles the closing tag for a function
     * @param  \Box\Brainy\Compiler\TemplateCompiler $compiler A compiler reference
     * @param  array|null                            $args     Arguments
     * @return mixed
     */
    public static function compileClose(\Box\Brainy\Compiler\TemplateCompiler $compiler, $args)
    {
        list($openTag) = self::closeTag($compiler, array('for', 'forelse'));

        if ($openTag == 'forelse') {
            return "}\n";
        } else {
            return "}\n}\n";
        }
    }
}
