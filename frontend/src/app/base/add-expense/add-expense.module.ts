import { NgModule } from '@angular/core';
import { MatSlideToggleModule, MatInputModule } from '@angular/material';

@NgModule({
    imports: [
        MatInputModule,
        MatSlideToggleModule
    ],
    exports: [
        MatInputModule,
        MatSlideToggleModule
    ]
})

export class AddExpenseModule { }