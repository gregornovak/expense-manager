import { Component } from '@angular/core';

@Component({
    selector: 'base',
    templateUrl: './base.component.html',
    styleUrls: ['./base.component.css']
})
export class BaseComponent {
    title = 'base';

    constructor() {}

    // ngOnInit() {
    //     this.getExpenses();
    // }
    //
    // private getExpenses() {
    //     this.expenseService.getAll().subscribe(expenses => { this.expenses = expenses; });
    // }
}
