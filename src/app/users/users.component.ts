import { Component, OnInit } from '@angular/core';
import { Router } from '@angular/router';
import { UserService } from '../user.service';

// import { AuthService } from '../auth.service';
import { HttpClient,HttpHeaders } from '@angular/common/http';

import {Http, Response, RequestOptions, Headers} from '@angular/http';
// import { Observable } from 'rxjs/Observable';
import { map } from "rxjs/operators";
import Swal from 'sweetalert2';
import * as $ from 'jquery';

@Component({
  selector: 'app-users',
  templateUrl: './users.component.html',
  styleUrls: ['./users.component.css']
})
export class UsersComponent implements OnInit {

   constructor(private http: Http,private User: UserService, private router: Router) { }

    heroes: any = []; 

    ngOnInit() {

        var user_type=sessionStorage.getItem('angular_admin_user_type');
        if(user_type!='master_admin'){
           this.router.navigate(['/login']);
        }

     	  this.User.get_users().subscribe(result => {
         	this.heroes=result;
           
        })
	  }

    delete_user($user_id){

      Swal({
      title: 'Are you sure?',
      text: 'You will not be able to recover this user!',
      type: 'warning',
      showCancelButton: true,
      confirmButtonText: 'Yes, delete it!',
      cancelButtonText: 'No, keep it'
    }).then((result) => {
      if (result.value) {

         this.User.delete_user($user_id).subscribe(result => {
            $('#'+$user_id).slideUp();
        })

        Swal(
          'Deleted!',
          'User has been deleted.',
          'success'
        )
      } else if (result.dismiss === Swal.DismissReason.cancel) {
        Swal(
          'Cancelled',
          'You canceled delete user',
          // 'error'
        )
      }
    })

    }
}
