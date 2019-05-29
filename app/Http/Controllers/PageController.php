<?php

namespace App\Http\Controllers;


class PageController extends Controller
{


    /**
     * 安全监控页面
     */
    public function aqjk()
    {
        return view('aqjk');
    }

    /**
     * 位置服务页面
     */
    public function wzfw()
    {
        return view('wzfw');
    }

    /**
     * 应急救援页面
     */
    public function yjjy()
    {
        return view('yjjy');
    }

    /**
     * 测试页面
     */
    public function test()
    {
        dd(TerminalUser::get());
    }

}
