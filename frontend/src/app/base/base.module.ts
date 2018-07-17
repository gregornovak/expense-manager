import { NgModule }                 from '@angular/core';
import { BrowserAnimationsModule }  from '@angular/platform-browser/animations';
import { MatToolbarModule }         from '@angular/material/toolbar';
import { MatIconModule }            from "@angular/material";
import { MatSidenavModule }         from '@angular/material/sidenav';

@NgModule({
    imports: [
        MatToolbarModule,
        MatIconModule,
        BrowserAnimationsModule,
        MatSidenavModule
    ],
    exports: [
        MatToolbarModule,
        MatIconModule,
        BrowserAnimationsModule,
        MatSidenavModule
    ]
})

export class BaseModule { }