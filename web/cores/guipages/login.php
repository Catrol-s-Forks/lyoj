<?php
function run(array $param,string& $html,string& $body):void {
    if (array_key_exists("logout",$param)) {
        setcookie("DedeUserID","",time()-1);
        setcookie("DedeUserID__ckMd5","",time()-1);
        setcookie("CSRF_TOKEN","",time()-1);
        setcookie("SESSDATA","",time()-1);
        $script="alert('Success!');";
        $script.="window.location.href=\"".($param["return"]!=""?$param["return"]:$_COOKIE["history"])."\";";
        $body.=InsertTags("script",null,$script);
        return;
    } $config=GetConfig();
    $login_controller=new Login_Controller;
    if ($login_controller->CheckLogin()) {
        $user_controller=new User_Controller;
        $info=$user_controller->GetWholeUserInfo($login_controller->CheckLogin());
        $script="alert(\"Have sign in as account '".$info["name"]."'\");";
        $script.="window.location.href=\"".($param["return"]!=""?$param["return"]:$_COOKIE["history"])."\";";
        $body.=InsertTags("script",null,$script);
        return;
    }
    $login=InsertTags("hp",array("style"=>InsertInlineCssStyle(array(
        "padding-right"=>"20px",
        "margin-bottom"=>"20px"
    ))),"Sign In");
    $login.=InsertTags("div",array("class"=>"flex","style"=>InsertInlineCssStyle(array(
        "padding-top"=>"10px"
    ))), InsertTags("p",null,"Account Email:&nbsp;").
    InsertSingleTag("input",array("id"=>"name","type"=>"text","placeholder"=>"Input account email here...")));
    $login.=InsertTags("div",array("class"=>"flex","style"=>InsertInlineCssStyle(array(
        "padding-top"=>"10px"
    ))), InsertTags("p",null,"Account Password:&nbsp;").
    InsertSingleTag("input",array("id"=>"passwd","type"=>"password","placeholder"=>"Input password here...")));
    $login.=InsertTags("div",array("class"=>"flex","style"=>InsertInlineCssStyle(array(
        "justify-content"=>"center"
    ))),InsertTags("button",array("onclick"=>"login()"),"Login"));
    $body.=InsertTags("div",array("class"=>"default_main","style"=>InsertInlineCssStyle(array(
        "padding-top"=>"20px",
        "padding-left"=>"20px",
        "padding-right"=>"20px",
        "padding-bottom"=>"15px",
        "margin-bottom"=>"20px",
        "width"=>"40%",
        "margin"=>"auto"
    ))),$login.InsertTags("a",array("href"=>GetUrl("register",null)),"Haven't an account? Register one!").
    InsertSingleTag("br",null).
    InsertTags("a",array("href"=>GetUrl("passwd",null)),"Forget your password? Reset it!"));
    $script="function strip_tags_pre(msg){msg=msg.replace(/<(\/)?pre[^>]*>/g,'');return msg;}";
    $script.="function setCookie(name,value,expire) {";
    $script.="var exp=new Date(); exp.setTime(exp.getTime()+expire);";
    $script.="document.cookie=name+\"=\"+escape(value)+\";expires=\"+exp.toGMTString()+\";\";}";
    $script.="function login(){";
    $script.="var name=document.getElementById('name').value;";
    $script.="var passwd=document.getElementById('passwd').value;";
    $script.="var salt=strip_tags_pre(SendAjax('".GetAPIUrl("/login/salt")."?email='+name,'GET',null));";
    $script.="if (JSON.parse(salt)['code']) {layer.msg(JSON.parse(salt)['message']); return false;}";
    $script.="salt=JSON.parse(salt)['data']['salt'];";
    $script.="var public_key=strip_tags_pre(SendAjax('".GetAPIUrl("/login/public")."','GET',null));";
    $script.="if (JSON.parse(public_key)['code']) {layer.msg(JSON.parse(public_key)['message']); return false;}";
    $script.="public_key=JSON.parse(public_key)['data']['key'];";
    $script.="var hash=strip_tags_pre(SendAjax('".GetAPIUrl("/tool/crypt")."','POST',{data:passwd+salt,key:public_key}));";
    $script.="if (JSON.parse(hash)['code']) {layer.msg(JSON.parse(hash)['message']); return false;}";
    $script.="hash=JSON.parse(hash)['data']['result'];";
    $script.="var res=strip_tags_pre(SendAjax('".GetAPIUrl("/login/password",null)."','POST',{email:name,passwd:hash}));";
    $script.="if (JSON.parse(res)['code']) {layer.msg(JSON.parse(res)['message']); return false;}";
    $script.="res=JSON.parse(res)['data'];";
    $script.="setCookie(\"DedeUserID\",res[\"DedeUserID\"],".$config["cookie_time"].");";
    $script.="setCookie(\"DedeUserID__ckMd5\",res[\"DedeUserID__ckMd5\"],".$config["cookie_time"].");";
    $script.="setCookie(\"CSRF_TOKEN\",res[\"CSRF_TOKEN\"],".$config["cookie_time"].");";
    $script.="setCookie(\"SESSDATA\",res[\"SESSDATA\"],".$config["cookie_time"].");";
    $script.="alert(\"Welcome back, \"+res[\"user\"][\"name\"]+\"!\");";
    $script.="window.location.href=\"".($param["return"]!=""?$param["return"]:$_COOKIE["history"])."\";";
    $script.="}";
    $script.="$(document).keypress(function(event){";
    $script.="var keynum=(event.keyCode?event.keyCode:event.which);  ";
    $script.="if(keynum=='13') login();";
    $script.="});";
    $body.=InsertTags("script",null,$script);
}
?>