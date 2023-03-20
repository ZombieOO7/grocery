var loaderFlag = true;
flagStatus(loaderFlag);

// $('#filterprojectId').select2({

//     ajax: {
//         url: 'https://etender.staging.causeway.com/tenderservices/api/estimator/project/composite/list',
//         dataType: 'json',
//         type: "PUT",
//         delay: 250,
//         enable: true,
//         readonly: false,
//         data: function (params) {
//             return JSON.stringify({
               
//                 "pageSize": 20,
//                 "totalRecord": 106,
//                 "currentPage": 1,
            
//                 "filters": [{
//                     "dataType": "STRING",
//                     "name": "name",
//                     "operand": "LIKE",
//                     "value": params.term
//                 }]
            
//             //searchTerm: params.term, // search term
//         });
//         },
//         beforeSend: function(xhr){
//             xhr.setRequestHeader('access-token', 'eyJ1c2VybmFtZSI6InJlaXNhbmcucmlzb21AY2F1c2V3YXkuY29tIiwiZXhwaXJ5IjowLCJvcmlnaW4iOiIxMC4yMC4xMC4yMyIsImlzRGV2aWNlIjpmYWxzZX0');
//             xhr.setRequestHeader('Content-Type', 'application/json');
//         },
//         processResults: function (data) {
//         return {
//             results: $.map(data.content, function (dt) {
//                 return {
//                     text: dt.name,
//                     id: dt.id
//                 }
//             })
//         };
//         },
//         cache: true,
//         minimumInputLength: 1
//     }
// });     




$(document).ready(function(){
    getResponderName();   
});

/**
 * Created by webclues-03 on 03-Mar-20.
 */
///sidebar js
$('.open_btn').on('click', function(e) {
    $('.sidebar').toggleClass("open"); //you can list several class names
    e.preventDefault();
});

$('.filter_btn').on('click', function(e) {
    $(".top-serch-filter-bar").show();
});

 // Initialize Cloud Firestore through Firebase
//  firebase.initializeApp({
//     apiKey: "AIzaSyC752WRswVF67c8xMD5ZmEd8Ld_88XsC2A",
//     authDomain: "project-hub-67711.firebaseapp.com",
//     databaseURL: "https://project-hub-67711.firebaseio.com",
//     projectId: "project-hub-67711",
//     storageBucket: "project-hub-67711.appspot.com",
//     messagingSenderId: "409016990193",
//     appId: "1:409016990193:web:4a95e423e289f1b7baec50",
//     measurementId: "G-Z5DHWPN622"
// });


/* apiKey: "AIzaSyBN_KD-OfeNYu3TCjhMHTj9T-wAasjCI-o",
authDomain: "chat-95f67.firebaseapp.com",
databaseURL: "https://chat-95f67.firebaseio.com",
projectId: "chat-95f67",
storageBucket: "chat-95f67.appspot.com",
messagingSenderId: "922192179308",
appId: "1:922192179308:web:547856d99f860494182849",
measurementId: "G-2RRBVH2G89"
 */

firebase.initializeApp({
    apiKey: "AIzaSyAeXoBTrVY2CkUV1FL5wopz-ONV2oK639Q",
    authDomain: "bid-solution-b4fac.firebaseapp.com",
    databaseURL: "https://bid-solution-b4fac.firebaseio.com",
    projectId: "bid-solution-b4fac",
    storageBucket: "bid-solution-b4fac.appspot.com",
    messagingSenderId: "146014001719",
    appId: "1:146014001719:web:c8d4cd55de6e2e3dcc3db2",
    measurementId: "G-KQ3R54L25Q"
});


var db = firebase.firestore();


// $(document).on('click','#sendMessage',function(e){
//     e.preventDefault();
//     debugger;
//     var groupId = $("#groupId").val();
//     var projectId = $("#projectId").val();
//     var subprojectId = $("#subprojectId").val();
//     var contractorId = $("#contractorId").val();
//     var messageText = $("#messageText").val();

//     if(messageText != ""){
//         sendMessage(projectId,subprojectId,contractorId)
//     }
// });

$(document).on('click','#newMessage',function(e){
    e.preventDefault();
 
    var filterprojectId = $("#filterprojectId option:selected").val();
    var filterpackageId = $("#filterpackageId option:selected").val();
    var filtercontractorId = $("#filtercontractorId option:selected").val();

    var filterpackageTitle = $("#filterpackageId option:selected").text();
    var projectTitle = $("#filterprojectId option:selected").text();
    var contractorName = $("#filtercontractorId option:selected").text();
    
   if(filterprojectId != "" && filterpackageId != "" && filtercontractorId != "0"){
        addCombination(filterprojectId,filterpackageId,filtercontractorId,filterpackageTitle,projectTitle,contractorName)
   } 
});

$(document).on('change','#filtercontractorId',function(e){
    
    var filterprojectId = $("#filterprojectId option:selected").val();
    var filterpackageId = $("#filterpackageId option:selected").val();
    var filtercontractorId = $("#filtercontractorId option:selected").val();

   if(filterprojectId != "" && filterpackageId != "" && filtercontractorId != "0"){
        checkCombination(filterprojectId,filterpackageId,filtercontractorId)
   }else{
        $("#newMessage").hide();
   }
    
});

$(document).on('change','#filterphresponderId',function(e){
    var filterphresponderId = $("#filterphresponderId option:selected").val();
    var search_messages = $("#search_messages").val();
    var searchcontractorId = $("#searchcontractorId option:selected").val();
    
    var fromDate = '';
    var toDate = '';
    if($("#startDate").val() != '' && $("#endDate").val() != ''){
        fromDate = new Date($("#startDate").val()).setHours(0,0,0,0) / 1000;
        toDate = new Date($("#endDate").val()).setHours(23,59,59,0) / 1000;
    }

    getProjectsByFilters(filterphresponderId,searchcontractorId,fromDate,toDate,search_messages,type="")
});

$(document).on('keyup','#search_messages',function(e){
    var search_string = $(this).val();
    var filterphresponderId = $("#filterphresponderId option:selected").val();
    var searchcontractorId = $("#searchcontractorId option:selected").val();

    var fromDate = '';
    var toDate = '';
    if($("#startDate").val() != '' && $("#endDate").val() != ''){
        // fromDate = new Date($("#startDate").val()).setHours(0,0,0,0) / 1000;
        // toDate = new Date($("#endDate").val()).setHours(23,59,59,0) / 1000;
        $("#startDate").val("");
        $("#endDate").val("");
    }
    
    getProjectsByFilters(filterphresponderId,searchcontractorId,fromDate,toDate,search_string,type="");
});

$(document).on('change','#filterprojectId',function(e){
    
    var filterprojectName = $("#filterprojectId option:selected").text();
    var filterprojectId = $("#filterprojectId option:selected").val();
    
   if(filterprojectId != ""){
        getdataByProjectId(filterprojectId,null,'subprojects',filterprojectName)
   }else{
       $("#filterpackageId").html('');
       $("#filterpackageId").selectpicker('refresh');

       $("#filtercontractorId").html('');
       $("#filtercontractorId").selectpicker('refresh');
       $("#newMessage").hide();
   }
    
});

$(document).on('change','#filterpackageId',function(e){
    
    var filterprojectId = $("#filterprojectId option:selected").val();
    var filtersubprojectId = $("#filterpackageId option:selected").val();
    var filterprojectName = $("#filterprojectId option:selected").text();
    
   if(filtersubprojectId != ""){
        getdataByProjectId(filterprojectId,filtersubprojectId,'contractors',filterprojectName)
   }else{
       
       $("#filtercontractorId").html('');
       $("#filtercontractorId").selectpicker('refresh');
       $("#newMessage").hide();
   }
    
});

$(document).on('click','.left_menu li',function(e){
    
    $(".left_menu li").removeClass('active');
    $(this).addClass('active');

    var projectId = $(this).attr("projectId");
    var subprojectId = $(this).attr("subprojectId");
    var contractorId = $(this).attr("contractorId");

    getConversationByProjectId(projectId,subprojectId,contractorId);   
});

$(document).on('click','#searchFilter',function(e){
    var filterphresponderId = $("#filterphresponderId option:selected").val();
    //var search_messages = $("#search_messages").val();
    $("#search_messages").val("");
    var searchcontractorId = $("#searchcontractorId option:selected").val();
    
    var fromDate = '';
    var toDate = '';
    if($("#startDate").val() != '' && $("#endDate").val() != ''){
        fromDate = new Date($("#startDate").val()).setHours(0,0,0,0) / 1000;
        toDate = new Date($("#endDate").val()).setHours(23,59,59,0) / 1000;
    }
   
    getProjectsByFilters(filterphresponderId,searchcontractorId,fromDate,toDate,search_messages="",type="")
});

$(document).on('click','#resetFilter',function(e){
    var filterphresponderId = $("#filterphresponderId option:selected").val();
    var search_messages = $("#search_messages").val();
    $("#searchcontractorId").val("").trigger("change");
    $("#startDate").val("");
    $("#endDate").val("");
    
    getProjectsByFilters(filterphresponderId,searchcontractorId="",fromDate="",toDate="",search_messages,type="")
});

function getProjectsByFilters(phResponderId="",contractorId="",fromDate="",toDate="",search_string="",type="",flagTemp=false) {
    

    let limit = $("#limit").val();
    
    let collectionContractors = db.collectionGroup("contractors");
    let flag = true;
    if(contractorId != ""){ 
        collectionContractors = collectionContractors.where('contractorId','==',parseInt(contractorId));
    }

    if(fromDate != "" && toDate != "" && fromDate != 0 && toDate != 0){ 
        collectionContractors = collectionContractors.where('messageDate','>=',parseInt(fromDate)).where('messageDate','<=',parseInt(toDate));  
    }

    if(phResponderId != "" && phResponderId != 0){ 
        collectionContractors = collectionContractors.where('phResponderId','==',parseInt(phResponderId));  
    }

    if(search_string != "" && search_string != 0){ 
        collectionContractors = collectionContractors.orderBy('lastMessage').startAt(search_string).endAt(search_string+'\uf8ff');  
        flag = false;
    }

    if(flag == true){
        //.limit(parseInt(limit))
        collectionContractors = collectionContractors.orderBy("messageDate","desc");
    }
    
    let leftmenuArray = [];
    let count = 0;
    collectionContractors.onSnapshot(contractorsSnap => {
        arrSize = contractorsSnap.size;
        contractorsSnap.forEach(contractorsDoc => {
            
            let contractorsData = contractorsDoc.data();
            let projectId = contractorsDoc.ref.parent.parent.parent.parent.id;
            let subprojectId = contractorsDoc.ref.parent.parent.id;
            
            leftmenuArray.push({
                contractorId:contractorsData.contractorId,
                lastMessage:contractorsData.lastMessage,
                messageDate:contractorsData.messageDate,
                phResponderId:contractorsData.phResponderId,
                projectTitle:contractorsData.projectTitle,
                unread:contractorsData.unread,
                projectId:projectId,
                subprojectId:subprojectId
            });
            count++;
        });

        

        if(count == arrSize){
            let leftmenuHtml = getHtml(leftmenuArray);
        
            // $(".inner_msg_list").html(leftmenuHtml);

            if($("#limit").val() <= arrSize){
                // $('.left_menu').scroll(function(){
                //     let ele = $(this);
                    
                //     if(ele.scrollTop() + ele.innerHeight() >= $(this)[0].scrollHeight) {

                //         let getLimit = $("#limit").val();
                //         $("#limit").val(parseInt(getLimit)+10);

                //         let phResponderId = $("#filterphresponderId option:selected").val();
                //         let search_string = $("#search_string").val();
                //         getProjectsByFilters(phResponderId,contractorId="",fromDate="",toDate="",search_string,type="",true);
                //     }     
                // });
            }
           
            if(type == "load"){
                $("ul.left_menu li:first-child").click();
            }
            getPhResponders(phResponderId);
        }
    });
  
}

function getPhResponders(phResponderId=""){
    let collectionContractors = db.collectionGroup("contractors");
        collectionContractors.onSnapshot(contractorsSnap => {
            let responderIds = [];
            contractorsSnap.forEach(contractorsDoc => {
                let contractorsData = contractorsDoc.data();

                responderIds.push(contractorsData.phResponderId);
            });
            //console.log(responderIds);
            
            let responderHtml = '<option value="">Select PH Responder</option>';
            $(array_unique(responderIds)).each(function( index, val ) { 
                let responderList = JSON.parse($("#responderList").val());
                
                let filterData = responderList.filter(function (person) { 
                    return person.id == val
                });
                
                if(typeof(filterData[0]) != "undefined" && filterData[0] !== null) {
                    if(phResponderId != "" && phResponderId == val){
                        selected = "selected";
                    }else{
                        selected = "";
                    }
                    responderHtml += '<option value="'+val+'" '+selected+'>'+filterData[0].userName+'</option>';
                }
            });
            
            $("#filterphresponderId").html(responderHtml);
            $("#filterphresponderId").selectpicker("refresh");
            
        });
        loaderFlag = false;
        flagStatus(loaderFlag);
}

function array_unique(array){
    return array.filter(function(el, index, arr) {
        return index == arr.indexOf(el);
    });
}

function getHtml(leftmenuArray){
    var leftmenuHtml = '<ul class="left_menu">';
    
    leftmenuArray.forEach(value => {
        
        if(value.messageDate != "0"){
            var timeStamp = convertTimestamp(value.messageDate,"left_menu");
        }else{
            var timeStamp = "";
        }

        var responderList = JSON.parse($("#responderList").val());
        
        var filterData = responderList.filter(function (person) { 
            return person.id == value.phResponderId 
        });
     

        if($("#projectId").val()==value.projectId && $("#subprojectId").val()==value.subprojectId && $("#contractorId").val()==value.contractorId){
            $activeClass = "active";
        }else{ 
            $activeClass = "";
        }
        
        leftmenuHtml += '<li projectId="'+value.projectId+'" subprojectId="'+value.subprojectId+'" contractorId="'+value.contractorId+'" class="'+$activeClass+'">';
        // leftmenuHtml += '<div class="menu_drpdwn dropdown">';
        // leftmenuHtml += '<button class="btn_dots dropdown-toggle" type="button" id="dropdownMenu2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-ellipsis-v" aria-hidden="true"></i></button>';
        // leftmenuHtml += '<div class="dropdown-menu" aria-labelledby="dropdownMenu2">';
        // leftmenuHtml += '<a class="dropdown-item" href="#">Hide Post</a>';
        // leftmenuHtml += '<a class="dropdown-item" href="#">Report</a>';
        // leftmenuHtml += '</div>';
        leftmenuHtml += '</div><div class="time-name">';
        if(typeof(filterData[0]) != "undefined" && filterData[0] !== null) {
            leftmenuHtml += '<h2 class="responderName_'+value.phResponderId+'">'+filterData[0].userName+'</h2>';
        }

        leftmenuHtml += '<h2></h2><p>'+timeStamp+'</p></div>';
        leftmenuHtml += '<h3>'+value.projectTitle+'</h3>';
        leftmenuHtml += '<div class="counting_text"><p>'+value.lastMessage+'</p>';
        if(value.unread > 0){
            leftmenuHtml += '<span class="counter_time">'+value.unread+'</span>';
        }
        
        leftmenuHtml += '</div></li>';
    });
    leftmenuHtml += '</ul>';

    return leftmenuHtml;
}

function getConversationByProjectId(projectId,subprojectId,contractorId) {
    $(this).find('.counter_time').hide();
    var collectionRef = db.collection("projects").doc(projectId).collection('subProjects').doc(subprojectId).collection('contractors').doc(contractorId);
    collectionRef.onSnapshot(snapshot2 => { 
        $data = snapshot2.data();
        
        //if(typeof($data.messageDetails) != "undefined" && $data.messageDetails !== null && $data.messageDetails.length > 0) {
            
            var msgHistory = '<ul class="msg_list">';
            $data.messageDetails.forEach(messageDetail => {
                if(contractorId == messageDetail.senderId){
                    //var json = JSON.parse($("#contractorsList").val());
                    
                    //var displayName = json[contractorId];
                    var displayName = $data.contractorName;
                    db.collection("projects").doc(projectId).collection("subProjects").doc(subprojectId).collection("contractors").doc(contractorId).update({
                        unread: 0
                    });                
                    
                    //alert(displayName);
                    var className = "lf_cnvstn";
                }else{
                    var obj = JSON.parse($("#responderList").val());
                    var filterData = obj.filter(function (person) { 
                        return person.id == messageDetail.senderId 
                    });

                    var displayName = "";
                    if(typeof(filterData[0]) != "undefined" && filterData[0] !== null) {
                        var displayName = filterData[0].userName; //phResponderId;
                    }
                    var className = "rt_cnvstn";
                }


                msgHistory += '<li class="'+className+'">';
                msgHistory += '<div class="name_time">';
                msgHistory += '<p>'+displayName+'</p><p>'+convertTimestamp(messageDetail.messageDateTime,"conversation")+'</p></div>';
                msgHistory += '<h4>'+messageDetail.messageText+'</h4></li>';
            });

            msgHistory += '<input type="hidden" id="projectId" value="'+projectId+'">';
            msgHistory += '<input type="hidden" id="subprojectId" value="'+subprojectId+'">';
            msgHistory += '<input type="hidden" id="contractorId" value="'+contractorId+'">';

            msgHistory += ' <div class="clearfix"></div>';
            
            $(".msg_box").html(msgHistory);
            $('.msg_box').animate({scrollTop: $('.msg_box').prop("scrollHeight")}, 100);
            loaderFlag = false;
            flagStatus(loaderFlag);
    });
}


function sendMessage(projectId,subprojectId,contractorId){
    var collectionRef = db.collection("projects").doc(projectId).collection('subProjects').doc(subprojectId).collection('contractors').doc(contractorId);

    var timeStamp = Math.floor(Date.now() / 1000);
    var messageText = $("#messageText").val();

    var senderId    = 0;
    if($("#userID").val() != ""){
        var senderId    = parseInt($("#userID").val());
    }
    
    var messageDetails = {messageDateTime:timeStamp,messageText:messageText,senderId:senderId};
    

    collectionRef.get().then(snapshot => {
       var snapData = snapshot.data();
        
       snapData['contractorId'] = snapData.contractorId;
       snapData['lastMessage'] = messageText;
       snapData['messageDate'] = timeStamp;
       snapData['phResponderId'] = senderId;
       snapData['projectTitle'] = snapData.projectTitle;
       snapData['unread'] = snapData.unread;
       snapData['readC'] = snapData.readC+1;
       
       snapData.messageDetails.push(messageDetails);
       
       $("#messageText").val("");
       
       //$('.msg_box').scrollTop($('.msg_box')[0].scrollHeight);
       $('.msg_box').animate({scrollTop: $('.msg_box').prop("scrollHeight")}, 100);
    
       collectionRef.set(snapData).then(function() {
            console.log("Added Successfully!");
            
            let phResponderId = $("#filterphresponderId option:selected").val();
            let search_string = $("#search_string").val();
            getProjectsByFilters(phResponderId,contractorId="",fromDate="",toDate="",search_string);

            loaderFlag = false;
            flagStatus(loaderFlag);
        }).catch(function(error) {
            console.error("Error: ", error);
        });
        
    });
    
}

function checkCombination(projectId,subprojectId,contractorId){ 
  
    db.collection("projects").doc(projectId).set({project:true});
    db.collection("projects").doc(projectId).collection('subProjects').doc(subprojectId).set({subProjects:true});
    var collectionRef = db.collection("projects").doc(projectId).collection('subProjects').doc(subprojectId).collection('contractors').doc(contractorId);
    
    collectionRef.onSnapshot(snapshot2 => {
        if(!snapshot2.exists){ 
           $("#newMessage").show();
           loaderFlag = false;
           flagStatus(loaderFlag);
        }
    });
}


function addCombination(projectId,subprojectId,contractorId,subprojectTitle,projectTitle,contractorName){
  
    db.collection("projects").doc(projectId).set({project:true,projectTitle:projectTitle});
    db.collection("projects").doc(projectId).collection('subProjects').doc(subprojectId).set({subProjects:true,subprojectTitle:subprojectTitle});
    var collectionRef = db.collection("projects").doc(projectId).collection('subProjects').doc(subprojectId).collection('contractors').doc(contractorId);
    
    var timeStamp = Math.floor(Date.now() / 1000);
    var projectTitle = $("#filterprojectId option:selected").text();

    collectionRef.onSnapshot(snapshot2 => {
        if(!snapshot2.exists){
            var c_id = parseInt(contractorId);
            var newCombination = {
                contractorId: c_id, 
                contractorName: contractorName,
                lastMessage: "",
                messageDate: timeStamp,
                messageDetails: [],
                phResponderId: 0,
                projectTitle: projectTitle,
                unread:0,
                readC:0
            };

            collectionRef.set(newCombination).then(function() {
                console.log("Added Successfully!");
                $("#filterprojectId").val('').trigger('change');
                $("#filterprojectId").selectpicker('refresh');

                let phResponderId = $("#filterphresponderId option:selected").val();
                let search_string = $("#search_string").val();

                getProjectsByFilters(phResponderId,contractorId="",fromDate="",toDate="",search_string);
                loaderFlag = false;
                flagStatus(loaderFlag);
            }).catch(function(error) {
                console.error("Error: ", error);
            });

        }
    });
}

function convertTimestamp(ts,type){
        // unix timestamp
        //var ts = Math.floor((new Date()).getTime() / 1000);

        // convert unix timestamp to milliseconds
        var ts_ms = ts * 1000;

        // initialize new Date object
        var date_ob = new Date(ts_ms);

        // year as 4 digits (YYYY)
        var year = date_ob.getFullYear();

        // month as 2 digits (MM)
        var month = ("0" + (date_ob.getMonth() + 1)).slice(-2);

        // date as 2 digits (DD)
        var date = ("0" + date_ob.getDate()).slice(-2);

        // hours as 2 digits (hh)
        var hours = ("0" + date_ob.getHours()).slice(-2);

        // minutes as 2 digits (mm)
        var minutes = ("0" + date_ob.getMinutes()).slice(-2);

        // seconds as 2 digits (ss)
        var seconds = ("0" + date_ob.getSeconds()).slice(-2);

        var tsDate = year + "-" + month + "-" + date;

        // date as YYYY-MM-DD format
        //console.log("Date as YYYY-MM-DD Format: " + year + "-" + month + "-" + date);

        //console.log("\r\n");

        // date & time as YYYY-MM-DD hh:mm:ss format: 
        //console.log("Date as YYYY-MM-DD hh:mm:ss Format: " + year + "-" + month + "-" + date + " " + hours + ":" + minutes + ":" + seconds);

        //console.log("\r\n");

        // time as hh:mm format: 
        //console.log("Time as hh:mm Format: " + hours + ":" + minutes);


        var todayDate = new Date().toISOString().slice(0,10);

        var dateString = todayDate;
        var date1 = new Date(dateString);
        var daysPrior = 1;
        date1.setDate(date1.getDate() - daysPrior);
        yesterday_date = date1.toISOString().slice(0,10); 

        if(todayDate == tsDate){
            if(type == 'conversation'){
                st = hours + ":" + minutes;
            }else{
                st = "Today " + hours + ":" + minutes;
            }
            
        }else if(yesterday_date == tsDate){
            st = "Yesterday";
        }else{
            st =  date + "/" + month + "/" + year;
        }
        return st;
}

function getResponderName(){
    var Url = "https://etender.staging.causeway.com/tenderservices/api/user/ph_admins";
    $.ajax({
        url: Url,
        type: 'GET',
        beforeSend: function(xhr){
            xhr.setRequestHeader('access-token', 'eyJ1c2VybmFtZSI6InJlaXNhbmcucmlzb21AY2F1c2V3YXkuY29tIiwiZXhwaXJ5IjowLCJvcmlnaW4iOiIxMC4yMC4xMC4yMyIsImlzRGV2aWNlIjpmYWxzZX0');
        },
        success: function(data) {
            
            $("#responderList").val(JSON.stringify(data));

            getProjectsByFilters("","","","","",type="load");
        }
        // ,
        // complete: function (data) {
        //     loaderFlag = false;
        //     flagStatus(loaderFlag);
        // }
    });
}

function getdataByProjectId(projectId,subprojectId=null,type,projectName=null){
   
    var jsonObjects = {
        "pageSize": 20,
        "totalRecord": 106,
        "currentPage": 1,
    
        "filters": [{
            "dataType": "STRING",
            "name": "name",
            "operand": "LIKE",
            "value": projectName
        }]
    };
//?ebdm=true
    var Url = "https://etender.staging.causeway.com/tenderservices/api/estimator/project/composite/list";
    loaderFlag = true;
    flagStatus(loaderFlag);
    $.ajax({
        url: Url,
        type: 'PUT',
        data: JSON.stringify(jsonObjects),
        dataType:"json",
        beforeSend: function(xhr){
            xhr.setRequestHeader('access-token', 'eyJ1c2VybmFtZSI6InJlaXNhbmcucmlzb21AY2F1c2V3YXkuY29tIiwiZXhwaXJ5IjowLCJvcmlnaW4iOiIxMC4yMC4xMC4yMyIsImlzRGV2aWNlIjpmYWxzZX0');
            xhr.setRequestHeader('Content-Type', 'application/json');
        },
        success: function(data) {
            
            if(type=='subprojects'){
                var subprojectsOptions = '';

                data.content.forEach(projectObj => {
                    if(projectObj.id == projectId){
                        
                        projectObj.tenders.forEach(subprojectObj => {
                            subprojectsOptions += '<option value="'+subprojectObj.id+'">'+subprojectObj.name+'</option>';

                        });
                    }
                });

                $("#filterpackageId").html(subprojectsOptions);
                $("#filterpackageId").selectpicker('refresh');
            }else{
                 
                var contractorOptions = '';
                
                data.content.forEach(projectObj => {
                    if(projectObj.id == projectId){
                        
                        projectObj.tenders.forEach(subprojectObj => {

                            if(subprojectObj.id == subprojectId){
                                subprojectObj.users.forEach(contractorObj => {
                                    contractorOptions += '<option value="'+contractorObj.organisationId+'">'+contractorObj.name+'</option>';
                                });
                            }
    
                        });
                    }
                });

                $("#filtercontractorId").html(contractorOptions);
                $("#filtercontractorId").selectpicker('refresh');

            }
        },
        complete: function (data) {
            loaderFlag = false;
            flagStatus(loaderFlag);
        }
    });
}

function flagStatus(status){
    if(status == true){ 
        $("#ajax_loader").show();
    }else{
        $("#ajax_loader").hide();
    }
}


getContractors();
function getContractors(){
    let collectionContractors = db.collectionGroup("contractors");

    collectionContractors.onSnapshot(contractorsSnap => {
        let contractorOptions = '<option value="">Select Contractor</option>';
        let contactorIdsArr = [];
        contractorsSnap.forEach(contractorsDoc => {
            $data = contractorsDoc.data();
            if(jQuery.inArray($data.contractorId, contactorIdsArr)) {
                contractorOptions += '<option value="'+$data.contractorId+'">'+$data.contractorName+'</option>';
                contactorIdsArr.push($data.contractorId);
            }
            
        });
        
        $("#searchcontractorId").html(contractorOptions);
    });
}

$(".selectpicker1").select2();

$('.datepicker1').datepicker({  
    autoclose:true,
    changeMonth: true,
    changeYear: true,
    format: 'yyyy.mm.dd',  
    todayBtn: 'linked'      
});
//$(".filter_btn").click(function(){
//    $(".top-serch-filter-bar").toggle();
//});
