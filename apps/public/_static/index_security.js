

/*
 * 
 * 
 * 


<script>



$('#ajax_passwd_form').submit(function(){
	
	$(this).ajaxSubmit({
        beforeSubmit:  checkLoginForm,
        
        success:       loginCallback,
        dataType: 'json'
    }); 
    return false; 
});
// 提交发布前验证
var checkLoginForm = function() {
	alter("88888888888888888888");
	if($('#oldpassword').val().length == 0) {
        $('#oldpassword').focus();
        return false;
    }
    if($('#password').val().length == 0) {
        $('#password').focus();
        return false;
    }
    if($('#repassword').val().length == 0) {
        $('#repassword').focus();
        return false;
    }
   
    return true;
};
// 成功后的回调函数
var loginCallback = function(i) {
    // var i = eval("("+e+")");
	 
    if(i.status==1){
       // $('#js_login_input').html('<p>'+i.info+'</p>').show();    
        if(i.data==0){
            window.location.href = U('public/Index/index');  
        }else{
            window.location.href = i.data;            
        }
    }else{
    	alert(i.info);
        //$('#js_login_input').html('<p>'+i.info+'</p>').show();
    }
};




</script>


$(document).ready(function() { 
    $('#ajax_passwd_form').submit(function(){
    	alert("$('#ajax_passwd_form').submit(function()");
    	ajaxSubmit('#ajax_passwd_form');
    });
   
    
    var ajaxSubmit = function(form) {
    	alert(form);
    	var args = M.getModelArgs(form);
    	M.getJS(THEME_URL + '/js/jquery.form.js', function() {
            var options = {
            	dataType: "json",
                success: function(txt) {
                	alert("34444444444444444443");
                    if(i.status==1){
                    	alert(i.info);
                       // $('#js_login_input').html('<p>'+i.info+'</p>').show();    
                        if(i.data==0){
                            window.location.href = U('public/Index/index');  
                        }else{
                            window.location.href = i.data;            
                        }
                    }else{
                    	alert(i.info);
                        //$('#js_login_input').html('<p>'+i.info+'</p>').show();
                    }
                }
            };
            $(form).ajaxSubmit(options);
    	});
    };
    
    
}); 
*/




/*// 提交表单
$(document).ready(function() { 
    $('#ajax_passwd_form').submit(function(){
    	alert("33333333333333");
    	$('#ajax_passwd_form').ajaxSubmit({
            beforeSubmit:  checkLoginForm, 
            success:       loginCallback,
            dataType: 'json'
        }); 
        return false; 
    });
    // 提交发布前验证
    var checkLoginForm = function() {
    	alert("34444444444444444443");
    	if($('#oldpassword').val().length == 0) {
            $('#oldpassword').focus();
            return false;
        }
        if($('#password').val().length == 0) {
            $('#password').focus();
            return false;
        }
        if($('#repassword').val().length == 0) {
            $('#repassword').focus();
            return false;
        }
       
        return true;
    };
    // 成功后的回调函数
    var loginCallback = function(i) {
        // var i = eval("("+e+")");
    	alert("34444444444444444443");
        if(i.status==1){
        	alert(i.info);
           // $('#js_login_input').html('<p>'+i.info+'</p>').show();    
            if(i.data==0){
                window.location.href = U('public/Index/index');  
            }else{
                window.location.href = i.data;            
            }
        }else{
        	alert(i.info);
            //$('#js_login_input').html('<p>'+i.info+'</p>').show();
        }
    };
}); 

*/
/*

$.post("{:U('public/Account/doModifyPassword')}", { }, function (i) {
	if(i.status==1){
		ui.success(i.info);
        if(i.data==0){
            window.location.href = U('public/Index/index');  
        }else{
            window.location.href = i.data;            
        }
    }else{
    	alert(i.info);
    	//ui.error(i.info);
        
    }
}, 'json');


*/