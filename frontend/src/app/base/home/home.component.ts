import { Component, OnInit } from '@angular/core';
import { ExpenseService }    from "../../services/expense.service";
import { Expense }          from "../../models/expense.model";

@Component({
    selector: 'home',
    templateUrl: './home.component.html',
    styleUrls: ['./home.component.css']
})
export class HomeComponent implements OnInit {
    title = 'home';
    private expenses : Expense[];

    constructor(private expenseService: ExpenseService) {}

    ngOnInit() {
        this.getExpenses();
    }

    private getExpenses(): void {
        this.expenseService.getAll().subscribe(
        result => {
                this.expenses = result;
            },
            error => {
                console.log(error);
            }
        );
    }

    displayedColumns: string[] = ['id', 'name', 'amount', 'payee', 'added'];
}
