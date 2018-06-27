import { BrowserModule } from '@angular/platform-browser';
import { NgModule } from '@angular/core';

import { AppComponent } from './app.component';
import { AppRoutingModule } from './app-routing.module';
import { LoginComponent } from "./login/login.component";
import { BaseComponent } from "./base/base.component";
import { HomeComponent } from "./base/home/home.component";

@NgModule({
    declarations: [
        AppComponent,
        HomeComponent,
        BaseComponent,
        LoginComponent
    ],
    imports: [
        BrowserModule,
        AppRoutingModule,
    ],
    providers: [],
    bootstrap: [ AppComponent ]
})
export class AppModule { }
