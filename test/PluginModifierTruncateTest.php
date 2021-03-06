<?php
/**
* Smarty PHPunit tests of modifier
*
* @package PHPunit
* @author Rodney Rehm
*/

namespace Box\Brainy\Tests;


class PluginModifierTruncateTest extends Smarty_TestCase
{
    public function testDefault() {
        $result = 'Two Sisters Reunite after Eighteen Years at Checkout Counter.';
        $tpl = $this->smarty->createTemplate('eval:{"Two Sisters Reunite after Eighteen Years at Checkout Counter."|truncate}');
        $this->assertEquals($result, $this->smarty->fetch($tpl));
    }

    public function testLength() {
        $result = 'Two Sisters Reunite after...';
        $tpl = $this->smarty->createTemplate('eval:{"Two Sisters Reunite after Eighteen Years at Checkout Counter."|truncate:30}');
        $this->assertEquals($result, $this->smarty->fetch($tpl));
    }

    public function testEtc() {
        $result = 'Two Sisters Reunite after';
        $tpl = $this->smarty->createTemplate('eval:{"Two Sisters Reunite after Eighteen Years at Checkout Counter."|truncate:30:""}');
        $this->assertEquals($result, $this->smarty->fetch($tpl));
    }

    public function testEtc2() {
        $result = 'Two Sisters Reunite after---';
        $tpl = $this->smarty->createTemplate('eval:{"Two Sisters Reunite after Eighteen Years at Checkout Counter."|truncate:30:"---"}');
        $this->assertEquals($result, $this->smarty->fetch($tpl));
    }

    public function testBreak() {
        $result = 'Two Sisters Reunite after Eigh';
        $tpl = $this->smarty->createTemplate('eval:{"Two Sisters Reunite after Eighteen Years at Checkout Counter."|truncate:30:"":true}');
        $this->assertEquals($result, $this->smarty->fetch($tpl));
    }

    public function testBreak2() {
        $result = 'Two Sisters Reunite after E...';
        $tpl = $this->smarty->createTemplate('eval:{"Two Sisters Reunite after Eighteen Years at Checkout Counter."|truncate:30:"...":true}');
        $this->assertEquals($result, $this->smarty->fetch($tpl));
    }

    public function testMiddle() {
        $result = 'Two Sisters Re..ckout Counter.';
        $tpl = $this->smarty->createTemplate('eval:{"Two Sisters Reunite after Eighteen Years at Checkout Counter."|truncate:30:"..":true:true}');
        $this->assertEquals($result, $this->smarty->fetch($tpl));
    }

}
