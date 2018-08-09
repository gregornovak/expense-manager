import { Component, OnInit } from '@angular/core';
import { ExpenseService }    from "../../services/expense.service";
import { Expense }           from "../../models/expense.model";
import { AlertService }      from "../../services/alert.service";

@Component({
    selector: 'home',
    templateUrl: './home.component.html',
    styleUrls: ['./home.component.css']
})
export class HomeComponent implements OnInit {
    title = 'home';
    private expenses : Expense[];

    constructor(private expenseService: ExpenseService, private alertService: AlertService) {}

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
            this.alertService.error(error);
        });
    }

    displayedColumns: string[] = ['id', 'name', 'amount', 'payee', 'added'];
}
