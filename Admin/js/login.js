function setCookie(cname,cvalue,exdays) {
    var d = new Date();
    d.setTime(d.getTime() + (exdays*24*60*60*1000));
    var expires = "expires=" + d.toGMTString();
    document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
}

$(document).ready(function(){
    $('#signup').on('click',function(){
        window.location.href = "http://ateefweb.com/mawaqit_al_salah/Admin/signup.html";
    });
    $('#login').on('click',function(){
        
    var username = $('#username').val();
    var password = $('#pass').val();
    var data = {'username' : username, 'pass' : password};
    $.ajax({
                        type:"post",
                        dataType:"json",
                        url:"http://ateefweb.com/mawaqit_al_salah/services/login",
                        data:data,
                        success:function(data){
                            //console.log(data);
                            if(data.msg == 'success'){
                                setCookie('username',username,1);
                                window.location.href = "http://ateefweb.com/mawaqit_al_salah/Admin/salah_update.html";
                            }
                            else{
                                $('#message').text(data.msg);
                            }
                        },
                        error: function(xhr2){
                            alert("An error occured: " + xhr2.status + " " + xhr2.statusText);
                        }
                    });
                    
    });
});