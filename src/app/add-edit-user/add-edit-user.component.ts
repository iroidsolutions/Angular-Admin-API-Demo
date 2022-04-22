import { Router,ActivatedRoute } from '@angular/router';
import Swal from 'sweetalert2';
import { Component, OnInit } from '@angular/core';
import { UserService } from '../user.service';
import { AuthService } from '../auth.service';
import { HttpClient,HttpHeaders } from '@angular/common/http';
import {Http, Response, RequestOptions, Headers} from '@angular/http';
import { map } from "rxjs/operators";

declare function checkFunction(): any;

@Component({
  selector: 'app-add-edit-user',
  templateUrl: './add-edit-user.component.html',
  styleUrls: ['./add-edit-user.component.css']
})
export class AddEditUserComponent implements OnInit {

  constructor(private Auth: AuthService,private User: UserService, private router: Router,private route: ActivatedRoute) { }

  user_detail: any = []; 

  validateUser: any = {};

  // validateUser:string;

  onSubmit() {
    alert("validate form");
  }

  ngOnInit() {
  	var user_type=sessionStorage.getItem('angular_admin_user_type');
	    if(user_type!='master_admin'){
	       this.router.navigate(['/login']);
	    }

  	
  		let user_id = this.route.snapshot.params['id'];
	  	if(user_id!=undefined){
	  		 // this._http.getRequest()
	  		this.get_user_byId(user_id)
	  	}else{
	  		this.user_detail=[{"user_id":"0","first_name":"","last_name":"","email_id":"","phone":""}];
	  	}
  }

  addUser($post) {

        checkFunction();
        // event.preventDefault()
        // // console.log("add user : "+JSON.stringify($post));

        // const first_name = $post.first_name;
        // const last_name = $post.last_name;
        // const email_id = $post.email_id;
        // const phone = $post.phone;
        // const user_id = $post.user_id;

        // if(first_name==''){
        //   Swal("First name is required.");
        //   return false;
        // }else if(last_name==''){
        //   Swal("Last name is required.");
        //   return false;
        // }else if(email_id==''){
        //   Swal("Email Id is required.");
        //   return false;
        // }


        // this.User.addUserDetails(first_name,last_name,email_id,phone,user_id).subscribe(data => {
        //   // console.log("admin component ts  : "+JSON.stringify(data));
        //   if(data==true) {
        //     this.router.navigate(['/users'])
        //   }else{
 			    //   Swal('Something is wrong!');
        //   }
        // })

    }
    get_user_byId($user_id){
    	this.User.get_user_byId($user_id).subscribe(result => {
            this.user_detail=result;
        })
    };

}

