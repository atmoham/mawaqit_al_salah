function getCookie(cname) {
    var name = cname + "=";
    var decodedCookie = decodeURIComponent(document.cookie);
    var ca = decodedCookie.split(';');
    for(var i = 0; i < ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0) == ' ') {
            c = c.substring(1);
        }
        if (c.indexOf(name) == 0) {
            return c.substring(name.length, c.length);
        }
    }
    return "";
}

var username = '';

$(document).ready(function(){
    
    var date = new Date();
    var current_day = date.getDate();
    var current_month_raw = date.getMonth() + 1;
    var current_year = date.getFullYear();
    var current_month = (current_month < 10) ? '0'+ current_month_raw : current_month_raw;
    current_day = (current_day < 10) ? '0'+ current_day : current_day;
    
    var to_month = Number(current_month) + 1;
    to_month = (to_month < 10) ? '0' + to_month : to_month;
    
    $('#from_date').val(current_month + "/" + current_day + "/" + current_year);
    $('#to_date').val(to_month + "/" + current_day + "/" + current_year);
    $('#current_date').text(current_month + "/" + current_day + "/" + current_year);
    
    username = getCookie('username');
    $('#username').text(username);
    
    $('#from_date').datepick();
    $('#to_date').datepick();
    
    var months_31 = [1,3,5,7,8,10,12]; //Month numbers that have 31 days.
    var more_days_check = $.inArray(current_month_raw,months_31);
    var more_days = (more_days_check > -1) ? 32 : 31;
    if(current_month_raw == 2){
        more_days = 29;
    }
    
    var j = Number(current_day);
    //To generate english dates for a month.
    for(var i = 0 ; i < 30; i++){
        if(j == more_days){
        j = 1;
        current_month_raw++;
        }
        $('#salah_table').append('<tr><td></td><td>'+ current_month_raw +'/'+ j + '/' + current_year +'</td></tr>');
        j++;
    }
    
    // To get masjid info
    var data = {'username' : username};
    $.ajax({
                        type:"post",
                        dataType:"json",
                        url:"http://ateefweb.com/mawaqit_al_salah/services/getMasjidInfo",
                        data:data,
                        success:function(data){
                            //console.log(data);
                            if(data.msg == 'success'){
                                $('#masjid_name').text(data.result['name']);
                                $('#masjid_address').text(data.result['city'] + ', ' + data.result['state'] + ', ' + data.result['country'] + ' - ' + data.result['zipcode']);
                                $('#masjid_details').text(data.result['website']);
                               // setCookie('username',username,1);
                               // window.location.href = "http://ateefweb.com/mawaqit_al_salah/Admin/salah_update.html";
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