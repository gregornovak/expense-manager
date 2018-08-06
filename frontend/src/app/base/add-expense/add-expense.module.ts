import { NgModule } from '@angular/core';
import { MatSlideToggleModule, MatInputModule, MatSelectModule, MatFormFieldModule } from '@angular/material';

@NgModule({
    imports: [
        MatInputModule,
        MatSlideToggleModule,
        MatFormFieldModule,
        MatSelectModule
    ],
    exports: [
        MatInputModule,
        MatSlideToggleModule,
        MatFormFieldModule,
        MatSelectModule
    ]
})

export class AddExpenseModule { }