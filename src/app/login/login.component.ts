import { Component, OnInit } from '@angular/core';
import { UserService } from '../user.service';
import { Router } from '@angular/router';
import { AuthService } from '../auth.service';
import { HttpClient,HttpHeaders } from '@angular/common/http';

import { NgModule } from '@angular/core';
import { BrowserModule } from '@angular/platform-browser';
import { FormGroup, FormArray, FormBuilder,
          Validators,ReactiveFormsModule  } from '@angular/forms';

import Swal from 'sweetalert2'
import * as jquery from 'jquery';

@Component({
  selector: 'app-login',
  templateUrl: './login.component.html',
  styleUrls: ['./login.component.css']
})
export class LoginComponent implements OnInit {

  constructor(private Auth: AuthService,private router: Router) { }

  ngOnInit() {
  }

  doLogin($post) {
        event.preventDefault()

        const email = $post.email;
        const password = $post.password;

        this.Auth.getUserDetails(email, password).subscribe(data => {
          // console.log("admin component ts  : "+JSON.stringify(data));
          if(data['flag']=='true') {

            sessionStorage.setItem('angular_admin_user_id',data['angular_admin_user_id']);
            sessionStorage.setItem('angular_admin_email_id',data['angular_admin_email_id']);
            sessionStorage.setItem('angular_admin_user_type',data['angular_admin_user_type']);
           
            // console.log("success: ");
            this.router.navigate(['/users'])
            this.Auth.setLoggedIn(true)
          }else{

 			      Swal('Invalid Email or Password');
          }
        })

    }

}

