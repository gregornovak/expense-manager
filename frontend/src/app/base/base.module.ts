import { NgModule } from '@angular/core';
import { BrowserAnimationsModule } from '@angular/platform-browser/animations';
import { MatToolbarModule } from '@angular/material/toolbar';
import { MatIconModule } from "@angular/material";

@NgModule({
    imports: [
        MatToolbarModule,
        MatIconModule,
        BrowserAnimationsModule
    ],
    exports: [
        MatToolbarModule,
        MatIconModule,
        BrowserAnimationsModule
    ]
})

export class BaseModule { }