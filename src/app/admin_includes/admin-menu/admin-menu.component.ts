import { Component, OnInit } from '@angular/core';
import { Router,ActivatedRoute } from '@angular/router';
import * as $ from 'jquery';

@Component({
  selector: 'app-admin-menu',
  templateUrl: './admin-menu.component.html',
  styleUrls: ['./admin-menu.component.css']
})
export class AdminMenuComponent implements OnInit {

  constructor(private router: Router, private activatedRoute:ActivatedRoute) {
  		this.router.events.subscribe((res) => { 
		    // console.log(this.router.url,"Current URL");

		    var segment=this.router.url.substr(1);

		    // var user_type=sessionStorage.getItem('angular_admin_user_type');

		    // if(user_type=='master_admin'){
		    // 	 // this.router.navigate(['/login']);
		    // 	 console.log("master admin called");
		    // }

		    if(segment=='adduser' || segment=='users' ){
		    	$('.adduser').addClass('active');
		    }
		   
		})
   }

  ngOnInit() {
  }

}
