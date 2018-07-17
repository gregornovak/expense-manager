import { Component, OnInit } from '@angular/core';
import { ExpenseService } from "../../services/expense.service";

@Component({
    selector: 'home',
    templateUrl: './home.component.html',
    styleUrls: ['./home.component.css']
})
export class HomeComponent implements OnInit {
    title = 'home';
    private expenses = [];

    constructor(private expenseService: ExpenseService) {}

    ngOnInit() {
        this.getExpenses();
    }

    private getExpenses() {
        this.expenseService.getAll().subscribe(expenses => { this.expenses = expenses.data; });
    }

    displayedColumns: string[] = ['id', 'name', 'amount'];
}
