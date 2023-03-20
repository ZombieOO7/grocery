firebase.initializeApp({
    apiKey: "AIzaSyB2_SI7jubC6RX0icg2pLprUwiNmyltw3Q",
    authDomain: "ipps-engineering-webclues.firebaseapp.com",
    databaseURL: "https://ipps-engineering-webclues.firebaseio.com",
    projectId: "ipps-engineering-webclues",
    storageBucket: "ipps-engineering-webclues.appspot.com",
    messagingSenderId: "954623288605",
    appId: "1:954623288605:web:17c547348788bb5908edf4",
    measurementId: "G-LV2J4X3QE2"
});
var db = firebase.firestore();

$.ajaxSetup({
    headers: {'X-CSRF-TOKEN': token}
});

db.collection("messages")
.orderBy('id','asc')
.onSnapshot(function(querySnapshot) {
    genHtml(querySnapshot);
});

db.collection("user_group_messages")
.orderBy('group_id','asc')
.onSnapshot(function(querySnapshot) {
    getMsgData();
});

db.collection("user_group_messages")
.orderBy('group_id','asc')
.get()
.then(function(querySnapshot) {
    getMsgData();
});

function getMsgData(){
    $(document).find('.left_menu li').each(function(){
        var groupId = $(this).attr('groupid');
        db.collection("user_group_messages")
        .where("group_id", "==", Number(groupId))
        .where("is_read","==",0)
        .where("admin_id","==",Number(adminId))
        .onSnapshot(function(querySnapshot) {
            var counter = 0;
            querySnapshot.forEach((doc) => {
                counter++;
            })
            if(counter > 0){
                var newhtml = '<span class="counter_time">'+counter+'</span>';
                $('.left_menu li[groupid="'+groupId+'"] .counting_text span').remove();
                $(newhtml).insertAfter('.left_menu li[groupid="'+groupId+'"] .counting_text p');
            }else{
                $('.left_menu li[groupid="'+groupId+'"] .counting_text span').remove();
            }
        });
    });
}


// search functionality
var next_index = 0;
var indexes;
var prev_index = 0;

// loader
function flagStatus(status){
    if(status == true){ 
        $("#ajax_loader").show();
    }else{
        $("#ajax_loader").hide();
    }
}
$(document).ready(function () {
    // flagStatus(true);
    getGroups();
})


//  Get Groups
function getGroups(groupId=null,string=null){
    $.ajax({
        url: getSideMenu,
        method: "POST",
        data:{
            id: groupId,
            message:string,
        },
        success: function (response) {
            $('.inner_msg_list').html(response);
            if(string !=null && string.length > 0){
                $('#fltr').css('display','contents');
            }
            var container = $('.inner_msg_list .left_menu'),scrollTo = $('li.active');
            getMsgData();
            flagStatus(false);
        }
    });
}
// get Active group message
$(document).on("click",".inner_msg_list li", function(){
    $(this).siblings('li').removeClass('active');
    $(this).addClass('active');
    var groupId = $(this).attr('groupid');
    getMessages2(groupId,null);
});

// search messages from groups 
$('#search_messages').keyup(delay(function (e) {
    var string = $.trim($(this).val());
    $('.msg_box').empty();
    $("#userId").val("").trigger("change");
    $('#startDate').val("");
    $('#endDate').val("");
    getGroups(null,string);
}, 500));

// search message from group
function searchString(string){
    prev_index = 0;
    next_index = 0;
    var span = $("#Test").find('span');
    if($(span).length) {
        $(span).contents().unwrap();
    }
    var find = $("#search_messages").val();
    var text = $(document).find(".serchMsg").text();
    var rg = new RegExp(find, 'gi');
    indexes = text.match(rg);
    $(document).find(".serchMsg").html(function(_, html){
        return html.replace(rg, function(x){
            return x+'<span class="red"></span>';                
        });   
    });
}


$("#next").on('click', function() {
    var val = $(document).find('#search_messages').val();
    var activeLi = $(document).find('.inner_msg_list .left_menu').find('li.active');
    if(val.length > 0 && activeLi.length > 0){
        $(document).find(".msg_box").find('span.red').eq(prev_index).removeClass('selected'); 
        if(prev_index > 0) {
        next_index = prev_index + 1;
        prev_index = -1;
        } else if (next_index > indexes.length - 1) {
            next_index = 0;
        }
        $(document).find(".msg_box").find('span.red').parent().eq(next_index-1).removeClass('selected');         
        $(document).find(".msg_box").find('span.red').parent().eq(next_index).addClass('selected');
        var $container = $(document).find(".msg_box");
        var $scrollTo = $(document).find(".msg_box").find('span.red').eq(next_index);
        $container.animate({scrollTop: $scrollTo.offset().top - $container.offset().top + $container.scrollTop(), scrollLeft: 0},300);
        next_index++;
    }
});

$("#prev").on('click', function() {
    var val = $('#search_messages').val();
    var activeLi = $('.inner_msg_list .left_menu').find('li.active');
    if(val.length > 0 && activeLi.length > 0){
        $(".msg_box").find('span.red').eq(next_index).removeClass('selected'); 
        if(next_index > 0) {
        prev_index = next_index - 1;
        next_index = -1;
        } else if (prev_index < 0) {
            prev_index = indexes.length - 1;
        }
        prev_index--;
        $(".msg_box").find('span.red').parent().eq(prev_index+1).removeClass('selected');
        $(".msg_box").find('span.red').parent().eq(prev_index).addClass('selected');
        var $container = $(".msg_box");
        var $scrollTo = $(".msg_box").find('span.red').eq(prev_index);
        $container.animate({scrollTop: $scrollTo.offset().top - $container.offset().top + $container.scrollTop(), scrollLeft: 0},300);
    }
 });
 $('#clr').on('click', function(){
    var val = $('#search_messages').val();
    $('#fltr').css('display','none');
    if(val.length > 0){
        $('.msg_box').empty();
        $('#search_messages').val('');
        getGroups();
    }
    $(".serchMsg").removeClass('selected');
 })

 $(document).on('click','#resetFilter',function(e){
    $('.msg_box').empty();
    $("#userId").val("").trigger("change");
    $("#startDate").val("");
    $("#endDate").val("");
    getGroups();
});

$('#userId').select2({
    placeholder: "Select member",
    ajax: {
        url: groupMembers,
        type: "get",
        dataType: 'json',
        delay: 250,
        data: function (params) {
          return {
            search: params.term // search term
          };
        },
        processResults: function (response) {
        return {
            results:  $.map(response.users, function (item,key) {
                    return {
                        text: item,
                        id: key
                    }
                })
            };
        },
        cache: true
    }
})
$('.filter_btn').on('click', function(e) {
    $(".top-serch-filter-bar").show();
});
$('.datepicker1').datepicker({  
    autoclose:true,
    changeMonth: true,
    changeYear: true,
    format: 'yyyy-mm-dd',  
    todayBtn: 'linked'      
});

$("#search_scn").validate({
    rules: {
        userId: "required",
        startDate: {
            required: function(){
                if($('#endDate').val().length > 0){
                    return true;
                }else{
                    return false;
                }
            },
        },
        endDate:{
            required: function(){
                if($('#startDate').val().length > 0){
                    return true;
                }else{
                    return false;
                }
            },
        }
    },
    errorPlacement: function (error, element) {
        if (element.attr("name") == "userId")
            error.insertAfter(".userError");
        else
            error.insertAfter(element);
    }
});
  

$(document).on('click','#searchFilter',function(e){
    $("#search_messages").val("");
    $('.msg_box').empty();

    var userId = $("#userId option:selected").val();
    if($("#search_scn").valid()){
        $.ajax({
            url: getSideMenu,
            method: "POST",
            data:{
                user_id: userId,
            },
            success: function (response) {
                $('.inner_msg_list').html(response);
                flagStatus(false);
                // display fileter buttons
                var string = $(document).find('#search_messages').val();
                if(string !=undefined && string !=null && string.length > 0){
                    $('#fltr').css('display','contents');
                }
                $('.inner_msg_list .left_menu').animate({scrollTop: $('li.active').prop("scrollHeight")}, 100);
            }
        });
    }
});

function delay(callback, ms) {
    var timer = 0;
    return function() {
      var context = this, args = arguments;
      clearTimeout(timer);
      timer = setTimeout(function () {
        callback.apply(context, args);
      }, ms || 0);
    };
  }

$(document).on('click','#sendMessage',function(e){
    e.preventDefault();
    var userids = $(".left_menu li.active").attr('userIds');
    var userIds = JSON.parse(userids);
    var groupId = $(".left_menu li.active").attr('groupId');
    var messageText = $("#messageText").val();

    var collection = db.collection("messages");
    var messages = collection.orderBy('id','desc').limit(1);
    messages.get().then((querySnapshot) => {
        querySnapshot.forEach((doc) => {
            var id = `${doc.data().id}`;
            var msgId = Number(id)+1;
            db.collection("messages").add({
                id:msgId,
                admin_id: Number(adminId),
                is_read:1,
                message:messageText,
                group_id:Number(groupId),
                created_at:time,
                updated_at:time,
                sender_id:null,
                sender_name:adminName,
            })
            .then(function(docRef) {
                $.each(userIds, function(key,val){
                    db.collection("user_group_messages").add({
                        user_id:Number(val),
                        is_read:0,
                        message_id:msgId,
                        group_id:Number(groupId),
                        admin_id:null
                    });
                })
                db.collection("user_group_messages").add({
                    user_id:null,                    
                    is_read:1,
                    message_id:msgId,
                    group_id:Number(groupId),
                    admin_id:Number(adminId)
                });

                $("#messageText").val('');
                getMessages2(groupId);
            })
            .catch(function(error) {
                console.error("Error adding document: ", error);
            });
        });
    });
});

$(document).ajaxStart(function () {  
    flagStatus(true);
    $('.main').off('click');
    $('.main').css('pointer-events', 'none');
}).ajaxStop(function () {  
    flagStatus(false);
    $('.main').css('pointer-events', 'auto');
});


function getMessages2(groupId){
    var userId = $("#userId option:selected").val();
    $('.msg_box').empty();
    if(userId != undefined && userId != null && userId !='' && $("#startDate").val() != '' && $("#endDate").val() != ''){
        var fDate = $("#startDate").val();
        var tDate = $("#endDate").val();
        var fromDate = fDate.replace("-", "");
        fromDate = fromDate.replace("-", "");
        var toDate = tDate.replace("-", "");
        toDate = toDate.replace("-", "");
        fromDate = Number(fromDate)*1000000;
        toDate = Number(toDate)*1000000;
        db.collection("messages")
        .where("group_id", "==", Number(groupId))
        .where("sender_id", "==", Number(userId))
        .where('created_at','>',fromDate)
        .where('created_at','<',toDate)
        .orderBy('created_at','asc')
        .onSnapshot(function(querySnapshot) {
            genHtml(querySnapshot);
        });
    }else if(userId != undefined && userId != null && userId !=''){
        db.collection("messages")
        .where("group_id", "==", Number(groupId))
        .where("sender_id", "==", Number(userId))
        .orderBy('created_at','asc')
        .onSnapshot(function(querySnapshot) {
            genHtml(querySnapshot);
        });
    
    }else{
        db.collection("messages")
        .where("group_id", "==", Number(groupId))
        .orderBy('id','asc')
        .onSnapshot(function(querySnapshot) {
            genHtml(querySnapshot);
        });
    }
    db.collection("user_group_messages")
        .where("group_id", "==", Number(groupId))
        .where("is_read","==",0)
        .where("admin_id","==",Number(adminId))
        .get()
        .then(function(querySnapshot) {
            querySnapshot.forEach((doc) => {
                db.collection("user_group_messages").doc(doc.id).update({
                    is_read: 1,
                })
            });
        });
}
function genHtml(querySnapshot){
    var html = '';
    html +='<ul class="msg_list">';
    var lastGrpId;
    querySnapshot.forEach((doc) => {
        var adminId= `${doc.data().admin_id}`;
        var created_at= convertTimestamp(`${doc.data().created_at}`);
        if(adminId != 'undefined' && adminId != undefined && adminId !=null){
            html += '<li class="rt_cnvstn">';
            html += '<div class="name_time">';
            html += '<p>'+`${doc.data().sender_name}`+'</p>';
            html += '<p>'+created_at+'</p>';
            html += '</div>';
            html += '<h4 class="serchMsg">'+`${doc.data().message}`+'</h4>';
            html += '</li>';
            html += '<div class="clearfix"></div>';
        }else{
            html += '<li class="lf_cnvstn">';
            html += '<div class="name_time">';
            html += '<p>'+`${doc.data().sender_name}`+'</p>';
            html += '<p>'+created_at+'</p>';
            html += '</div>';
            html += '<h4 class="serchMsg">'+`${doc.data().message}`+'</h4>';
            html += '</li>';
            html += '<div class="clearfix"></div>';
        }
        var counter = 0;
        var lastMsg = `${doc.data().message}`;
        lastGrpId = `${doc.data().group_id}`;
        $(document).find('.left_menu li[groupid="'+lastGrpId+'"] .counting_text p').text(lastMsg);
        $(document).find('.left_menu li[groupid="'+lastGrpId+'"] .time-name p').text(created_at);
    });
    html += '</ul>';
    if($('.inner_msg_list li.active').length > 0){
        var activeGroupId = $('.inner_msg_list li.active').attr('groupid');
        if(lastGrpId == activeGroupId){
            $('.msg_box').empty().append(html);
            $('.msg_box').animate({scrollTop: $('.msg_box').prop("scrollHeight")}, 100);
        }
    }
    var string = $(document).find('#search_messages').val();
    if(string.length > 0 && string != null)
        searchString(string);
    // flagStatus(false);
    var unReadMsg = $(document).find('.inner_msg_list li.active').attr('data-unread-count');
    if(unReadMsg > 0){
        $(document).find('.inner_msg_list li.active').find('.counting_text .counter_time').css('display','none');
    }
}

function convertTimestamp(ts){
    var year = ts.substring(0, 4);
    var month = ts.substring(4, 6);
    var date = ts.substring(6, 8);
    
    var hours = ts.substring(8, 10);
    var minutes = ts.substring(10, 12);
    var seconds = ts.substring(12, 14);

    var tsDate = year + "-" + month + "-" + date;

    var todayDate = new Date().toISOString().slice(0,10);

    var dateString = todayDate;
    var date1 = new Date(dateString);
    var daysPrior = 1;
    date1.setDate(date1.getDate() - daysPrior);
    yesterday_date = date1.toISOString().slice(0,10); 

    if(todayDate == tsDate){
        st = "Today " + hours + ":" + minutes;        
    }else if(yesterday_date == tsDate){
        st = "Yesterday";
    }else{
        st =  date + "-" + month + "-" + year;
    }
    return st;
}
