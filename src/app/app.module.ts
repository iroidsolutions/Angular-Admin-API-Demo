import { BrowserModule } from '@angular/platform-browser';
import { NgModule } from '@angular/core';
import { RouterModule,Routes } from '@angular/router';
import { AppComponent } from './app.component';
import { AuthService } from './auth.service';
import { UserService } from './user.service';
import { LoginComponent } from './login/login.component';
import { FormsModule, ReactiveFormsModule } from '@angular/forms';
import { HttpClientModule } from '@angular/common/http';
import { HttpModule } from '@angular/http';
import * as $ from 'jquery';
import { UsersComponent } from './users/users.component';
import { AdminHeaderComponent } from './admin_includes/admin-header/admin-header.component';
import { AdminMenuComponent } from './admin_includes/admin-menu/admin-menu.component';
import { AddEditUserComponent } from './add-edit-user/add-edit-user.component';
import { ApiComponent } from './api/api.component';


@NgModule({
  declarations: [
    AppComponent,
    LoginComponent,
    UsersComponent,
    AdminHeaderComponent,
    AdminMenuComponent,
    AddEditUserComponent,
    ApiComponent,
  ],
  imports: [
    BrowserModule,
    ReactiveFormsModule,
    FormsModule,
    HttpClientModule,
    HttpModule,
    RouterModule.forRoot([
      {
        path: '',
        component: LoginComponent
      },
      {
        path: 'api',
        component: ApiComponent,
      },
      {
        path: 'login',
        component: LoginComponent,
      },
      {
        path: 'users',
        component: UsersComponent,
      },
      {
        path: 'addUser',
        component: AddEditUserComponent
        // canActivate: [AuthGuard]
      },
      {
        path: 'editUser/:id',
        component: AddEditUserComponent
        // canActivate: [AuthGuard]
      },
    ])
  ],
  providers: [AuthService,UserService],
  bootstrap: [AppComponent]
})

export class AppModule { }
