import { Injectable } from '@angular/core';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import 'core-js';
import 'zone.js/dist/zone';
import * as jquery from 'jquery';

interface myData {
  success: boolean,
  message: string,
  store_id:string
}

const httpOptions = {
  headers: new HttpHeaders({ 'Content-Type': 'application/json' })
};


@Injectable()
export class AuthService {

  private loggedInStatus = false

  constructor(private http: HttpClient) { }


  setLoggedIn(value: boolean) {
    this.loggedInStatus = value
  }


  get isLoggedIn() {
    return this.loggedInStatus
  }

  getUserDetails(email, password) {
    // let headers = new Headers();
    // headers.append('Content-Type', 'application/x-www-form-urlencoded; charset=UTF-8');
    const headers = new HttpHeaders().set("content-type", "application/json");

    let data = {
        email: email,
        password: password
    };


    // return this.http.post('http://localhost/dynamic-form/test/api/auth1.php', {
  
    //   email,
    //   password
    // })

     // return this.http.post('http://localhost/dynamic-form/test/api/auth1.php', FormData1)
    const body = JSON.stringify({email: email, password: password,action:'login_admin'});
    return this.http.post('http://localhost/angular/php/admin/admin_ops.php', jquery.parseJSON(body), httpOptions);

  }
  

}
