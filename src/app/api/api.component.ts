import { Component, OnInit } from '@angular/core';
import { AuthService } from '../auth.service';
import { Injectable } from '@angular/core';
import { HttpClient,HttpHeaders } from '@angular/common/http';
import { Observable, of } from 'rxjs';
import { map } from 'rxjs/operators';
import { Http, Response, Headers, RequestOptions } from "@angular/http";
import { Router } from '@angular/router';
declare var jquery:any;
import * as $ from 'jquery';

interface myData {
  message: string,
  Headers:string

}

@Component({
  selector: 'app-api',
  templateUrl: './api.component.html',
  styleUrls: ['./api.component.css']
})



export class ApiComponent implements OnInit {

    constructor(private Auth: AuthService, 
              private router: Router,private http: HttpClient) { }


    ngOnInit() {


            var api_id = '6baf4b8b40706cfff6b9926d3ec32a50';
            var api_secret = '879a16d54d2558ee28b83dd71d8af83d';
            var testcases = [];


            testcases.push({
                api_id: api_id,
                api_secret: api_secret,
                api_request: '--- GENERAL SERVICES ---',
                data: {
                }
            });

            testcases.push({
                api_id: api_id,
                api_secret: api_secret,
                api_request: 'sign_up',
                data: {
                    'user_name':'user_name',
                    'first_name':'first_name',
                    'last_name':'last_name',
                    'email_id': 'iroid.test1@gmail.com',
                    'password': '098f6bcd4621d373cade4e832627b4f6', 
                    'date_of_birth':'1991-01-01',
                    'gender':'1:Male 0:Female',
                    'interested_tag':'1,2',
                    'profile_pic':'user_profile.png',
                    'time_zone':'Asia/Kolkata',
                    'secret': '7629660670a3ad7613e56f048e217c10'
                }
            });

            testcases.push({
                api_id: api_id,
                api_secret: api_secret,
                api_request: 'login',
                data: {
                    'email_id': 'iroid.test1@gmail.com',
                    'password': '098f6bcd4621d373cade4e832627b4f6',
                    'secret': '7629660670a3ad7613e56f048e217c10'                    
                }
            });

          
            testcases.push({
                api_id: api_id,
                api_secret: api_secret,
                api_request: 'forgot_password',
                data: {        
                    'email_id': 'iroid.test1@gmail.com'            
                }
            });

            testcases.push({
                api_id: api_id,
                api_secret: api_secret,
                api_request: 'change_password',
                data: {        
                    'email_id': 'iroid.test1@gmail.com',
                    'password':'098f6bcd4621d373cade4e832627b4f6'           
                }
            });

            testcases.push({
                api_id: api_id,
                api_secret: api_secret,
                api_request: '--- USER PROFILE & SETTINGS SERVICES ---',
                data: {
                }
            });

            testcases.push({
                api_id: api_id,
                api_secret: api_secret,
                api_request: 'update_user_profile',
                data: {
                    'user_id': '1',
                    'user_name':'test',
                    'first_name':'test',
                    'last_name':'test',
                    'password':'098f6bcd4621d373cade4e832627b4f6',
                    'profile_pic':'user_profile.png',
                    'gender':'1:male|0:female',
                    'profile_privacy':'0:Public,1:private'                         
                }
            });

            testcases.push({
                api_id: api_id,
                api_secret: api_secret,
                api_request: 'logout_user',
                data: {
                    'user_id': '1',
                    'device_type': 'ios'
                }
            });

            testcases.push({
                api_id: api_id,
                api_secret: api_secret,
                api_request: '--- NOTIFICATION SERVICES ---',
                data: {
                }
            });
        
            testcases.push({
                api_id: api_id,
                api_secret: api_secret,
                api_request: 'register_for_push',
                data: {
                    'user_id': '1',
                    'device_token': '212345364',
                    'certificate_type' : '0 where 0:dev, 1:live ' 
                }
            });

            testcases.push({
                api_id: api_id,
                api_secret: api_secret,
                api_request: 'get_user',
                data: {
                    'user_id': '1'
                }
            });

            $(document).ready(function(){
              console.log("document ready");


                for (var i = 0; i < testcases.length; i++) {
                    $('.select').append('<option value="' + i + '">' + testcases[i].api_request + '</option>');
                }

                $('.select').change(function () {

                    if($('.select').val() <= 5){
                      $('#Authorization').val('');  
                    }else{
                        var access_token =  localStorage.getItem("access_token");
                        if(access_token !=""){
                            $('#Authorization').val(access_token);
                        }
                    }
                    // console.log("access_token : "+access_token);

                    if($('.select option:selected').text() == "get_user_from_likes"){
                        $('.get_user_from_likes').show(); 
                    }else{
                        $('.get_user_from_likes').hide();
                    }
                    if($('.select option:selected').text() == "send_accept_frined_request"){
                        $('.send_accept_frined_request').show(); 
                    }else{
                        $('.send_accept_frined_request').hide();
                    }
                    if($('.select option:selected').text() == "get_users"){
                        $('.get_users').show(); 
                    }else{
                        $('.get_users').hide();
                    }

                    if ($('.select').val() != -1) {

                        // console.log($('.select').val());
                        var selected_val=$('.select').val();

                        var selected_testcase=JSON.stringify($('.select').val());
                        var intval=selected_testcase.replace(/['"]+/g, '');
 
                        $('.testcase').val(JSON.stringify(testcases[intval], null, 4));
                        $('.output').val('');
                    }
                });

            });  

    }

    run_testcase() {
        // console.log("run case : "+$('.testcase').val());
        try {
            var data = JSON.parse($('.testcase').val());
            // var data=jQuery.parseJSON(JSON.stringify($('.testcase').val()));
            var test=$('.testcase').val();

        } catch (e) {
            console.log("catch");
            $('.output').val('Invalid JSON.');
            return;
        }
        // console.log("data : "+data);

        if (data) {
            let Devicetype: string | null = "";
            let Deviceid: string | null = "";
            let Authorization: string | null = "";

            let access_token : string | null = "";

            Authorization = '"'+$('#Authorization').val()+'"';
            Devicetype  = '"'+$('.device_type').val()+'"';
            Deviceid = '"'+$('.device_id').val()+'"';
            // var request=jQuery.parseJSON(data)['api_request'];
            var request=data.api_request;

            if(request != 'login' && request != 'sign_up' && request != 'verify_code' && request != 'change_password' && request != 'forgot_password'){
                if(Authorization == ""){
                    console.log('Api','Authorization can not be empty','warning');
                    return false;
                }
                if(Devicetype == ""){
                    console.log('Api','Device type can not be empty.','warning');
                    return false;
                }
                if(Deviceid == ""){
                    console.log('Api','Device id can not be empty.','warning');
                    return false;
                }
            }



            // **************

            // const userStr = JSON.stringify($('.testcase').val());
            // console.log("parse : ",JSON.parse(userStr));

            // module Ajax {
                // export class Options {
                //     url: string;
                //     method: string;
                //     data: Object;
                //     constructor(url: string, method?: string, data?: Object) {
                //         this.url = url;
                //         this.method = method || "get";
                //         this.data = data || {};
                //     }
                // }
            // }


            if(Authorization=='"undefined"'){
                Authorization="";
            }else{
                Authorization=Authorization.slice(1, -1);
            }
                       
            Devicetype=Devicetype.slice(1, -1);
            Deviceid=Deviceid.slice(1, -1);

            $.ajax({
                method: 'POST',
                url: 'http://localhost/angular/php/api/api.php',
                headers: { 'Authorization': Authorization, 'Devicetype': Devicetype, 'Deviceid': Deviceid},
                data: data,

                success: function (responsejson) { 

                    // console.log("response : "+responsejson);
                    var response=responsejson;

                    if (typeof responsejson == 'string' || responsejson instanceof String) {
                        try {
                            var output = JSON.parse(response);
                            $('.output').val(response);
                            access_token=output['data']['access_token'];

                            if(request == 'login'){
                                access_token = 'tiger'+' '+access_token;
                                $('#Authorization').val(access_token);
                                localStorage.setItem("access_token", access_token);
                            }   

                            if(request == 'logout_user'){
                               if(output['flag']=='true'){
                                   localStorage.setItem("access_token", '');
                                   $('#Authorization').val('');
                               }
                            }    

                        } catch (e) {
                            $('.output').val(response);
                        }
                    } else {
                        $('.output').val(JSON.stringify(response, null, 4));
                        $('.output').val(response);
                    }
                    if ($('.output').val() == '') {
                        $('.output').val('No output.');
                    }
                },
                error: function (data, status, error_thrown) {
                    $('.output').val('Error: ' + error_thrown);
                }
            });
        }
    }

}

