import { MatInputModule }  from '@angular/material/input';
import { MatCardModule }   from '@angular/material/card';
import { MatButtonModule } from '@angular/material/button';
import { BrowserAnimationsModule } from '@angular/platform-browser/animations';
import { NgModule }        from '@angular/core';

@NgModule({
    imports: [
        MatInputModule,
        MatCardModule,
        MatButtonModule,
        BrowserAnimationsModule
    ],
    exports: [
        MatInputModule,
        MatCardModule,
        MatButtonModule,
        BrowserAnimationsModule
    ]
})

export class LoginModule { }