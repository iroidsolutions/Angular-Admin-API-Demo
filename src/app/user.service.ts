import { Injectable } from '@angular/core';
import { Observable, of } from 'rxjs';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import * as jquery from 'jquery';

interface myData {
  message: string,
  success: boolean,
  store_id:string,
  selected_val:string
}
const httpOptions = {
  headers: new HttpHeaders({ 'Content-Type': 'application/json' })
};

interface isLoggedIn {
  status: boolean
}

interface logoutStatus {
  success: boolean
}

interface adminpanel {
  success: boolean
}
@Injectable()
export class UserService {

  constructor(private http: HttpClient) { }

  getSomeData() {
    return this.http.get<myData>('http://localhost/angular/php/api/db_connection.php')
  }

  isLoggedIn(): Observable<isLoggedIn> {
    return this.http.get<isLoggedIn>('/api/isloggedin.php')
  }

  get_users() {

    const headers = new HttpHeaders().set("content-type", "application/json");
    const body = JSON.stringify({action:'get_user'});
    return this.http.post('http://localhost/angular/php/admin/admin_ops.php', jquery.parseJSON(body), httpOptions);
  }

  addUserDetails(first_name,last_name,email_id, phone,user_id) {
    const headers = new HttpHeaders().set("content-type", "application/json");

    const body = JSON.stringify({first_name: first_name,last_name: last_name,email_id: email_id, phone: phone,user_id: user_id,action:'addEdit_user'});
    return this.http.post('http://localhost/angular/php/admin/admin_ops.php', jquery.parseJSON(body), httpOptions);
  }

  get_user_byId(user_id){
    
    const headers = new HttpHeaders().set("content-type", "application/json");
    const body = JSON.stringify({user_id: user_id,action:'get_user_byId'});
    return this.http.post('http://localhost/angular/php/admin/admin_ops.php', jquery.parseJSON(body), httpOptions);
  }

  delete_user(user_id){
    
    const headers = new HttpHeaders().set("content-type", "application/json");
    // return this.http.get('http://localhost/dynamic-form/test/api/get_user_byId.php')
    const body = JSON.stringify({user_id: user_id,action:'delete_user'});
    return this.http.post('http://localhost/angular/php/admin/admin_ops.php', jquery.parseJSON(body), httpOptions);
  }




}
