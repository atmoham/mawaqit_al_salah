    var country = '';
    var state = '';
    var city = '';
    var street = '';
    var zipcode = '';
    var new_masjid_flag = false;


$(document).ready(function(){
    
   // $('#signup').attr('disabled',true);
    
    $('#search').on('click',function(){
        country = $('#country').val();
        state = $('#state').val();
        city = $('#city').val();
        
        zipcode = $('#zipcode').val();
        
        if(country == ''){
            alert('Please select a country.');
        }
        else if((state != '' && city != '') || zipcode != 0){
          
            if(zipcode == 0)
                zipcode = 0;
        
            var data = {'country' : country, 'state' : state, 'city' : city, 'zipcode' : zipcode};
        
            $.ajax({
                        type:"post",
                        dataType:"json",
                        url:"http://127.0.0.1:8888/Mawaqit_al_salah/services/masjid_lookup",
                        data:data,
                        success:function(data){
                            if(data.msg == 'success'){
                                 $('#masjid_name').css('display','block');
                                $.each( data.result, function( key, value ) {
                                    
                                      $('#masjid').append($("<option></option>").attr("value",value['id']).text(value['name']));  
                                    
                                       
                                });
                               
                            }
                            else{
                                //$('#message').text(data.msg);
                            }
                        },
                        error: function(xhr2){
                            alert("An error occured: " + xhr2.status + " " + xhr2.statusText);
                        }
            });
        }
        else{
            alert('Please (select state and enter city) or (enter zipcode).');
        }
        
    });
    
    $('#masjid').on('change', function(){
        var new_masjid_sel = $('#masjid :selected').val();
        
        if(new_masjid_sel == 1){
            new_masjid_flag = true;
           // $("body").scrollTop(0);
            $('.new_masjid').css('display','block');
            $('#search').css('display','none');
            $('#lookup_text').text('Masjid Details');
            $('#state_text').text('State *:');
            $('#city_text').text('City *:');
            $('#zip_text').text('Zipcode *:');
            $('#or').css('display','none');
            //$('#search').css('display','none');
            $('#masjid_name').css('display','none');
            $('#signup').css('display','block');
            
        }
        else{
            new_masjid_flag = false;
            $('#lookup_text').text('Masjid Details');
            $('#search').css('display','none');
            $('#or').css('display','none');
            $('#signup').css('display','block');
        }
        
    });
    
    $('#signup').on('click',function(){
      //alert();
    var username = $('#username').val();
    var password = $('#pass').val();
    var cpassword = $('#cpass').val();
    var name = $('#name').val();
    var phone_no = $('#phone_no').val();
    var masjid_no = $('#masjid_no').val();
    var masjid_name ='';
    street = $('#street').val();
    country = $('#country').val();
    state = $('#state').val();
    city = $('#city').val();
    
    if(new_masjid_flag == true){
        masjid_name = $('#new_masjid_name').val();
    }
    else{
        masjid_name = $('#masjid option:selected').text();
    }
    
    
    if(new_masjid_flag == true &&  (masjid_name == '' || zipcode == '') || country == '' || state == '' || city == '' || name == '' || password == '' || username =='' || cpassword == '') {
        alert('Please fill the required * fields.');
    }
    else{
        
    var data = {'username' : username, 'pass' : password, 'name' : name, 'phone_no' : phone_no, 'masjid_no' : masjid_no, 'masjid_name' : masjid_name, 'country' : country, 'state' : state, 'city' : city, 'street' : street, 'zipcode' : zipcode};
    
    $.ajax({
                        type:"post",
                        dataType:"json",
                        url:"http://127.0.0.1:8888/Mawaqit_al_salah/services/signup",
                        data:data,
                        success:function(data){
                            //console.log(data);
                            if(data.msg == 'success'){
                                $('#message').text(data.result);
                                //window.location.href = "http://ateefweb.com/mawaqit_al_salah/Admin/salah_update.html";
                            }
                            else{
                                $('#message').text(data.msg);
                            }
                        },
                        error: function(xhr2){
                            alert("An error occured: " + xhr2.status + " " + xhr2.statusText);
                        }
                    });
    }       
    });
    
});