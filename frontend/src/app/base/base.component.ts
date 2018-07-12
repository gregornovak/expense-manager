import { Component, OnInit } from '@angular/core';
import { ExpenseService } from "../services/expense.service";

@Component({
    selector: 'base',
    templateUrl: './base.component.html',
    styleUrls: ['./base.component.css']
})
export class BaseComponent implements OnInit {
    title = 'base';
    private expenses = [];

    constructor(private expenseService: ExpenseService) {}

    ngOnInit() {
        this.getExpenses();
    }

    private getExpenses() {
        this.expenseService.getAll().subscribe(expenses => { this.expenses = expenses; console.log(expenses); });
    }
}
