<?php

function rq($key = null)
{
    return ($key == null) ? \Illuminate\Support\Facades\Request::all() : \Illuminate\Support\Facades\Request::get($key);
}

/**
 * @param null $data
 * @return array 成功返回0
 */
function suc($data = null)
{
    $ram = ['status' => 0];
    if ($data) {
        $ram['data'] = $data;
        return $ram;
    }
    return $ram;
}

/**
 * @param $code
 * @param null $data
 * @return array 失败返回错误码和信息
 */
function err($code, $data = null)
{
    if ($data)
        return ['status' => $code, 'data' => $data];
    return ['status' => $code];
}


Route::group(['middleware' => 'web'], function () {


//    Route::get('test', 'PageController@test');//测试


    Route::get('aqjk', 'PageController@aqjk');//安全监控
    Route::get('wzfw', 'PageController@wzfw');//位置服务
    Route::get('yjjy', 'PageController@yjjy');//应急救援


});

Route::get('/home', 'PageController@index');
