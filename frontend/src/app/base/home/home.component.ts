import { Component, OnInit, ViewChild } from '@angular/core';
import { PageEvent, MatPaginator }      from '@angular/material';
import { ExpenseService }               from '../../services/expense.service';
import { Expenses }                     from '../../models/expenses.model';
import { AlertService }                 from '../../services/alert.service';
import { LoadingComponent }             from '../../shared/loading/loading.component'

@Component({
    selector: 'home',
    templateUrl: './home.component.html',
    styleUrls: ['./home.component.css']
})
export class HomeComponent implements OnInit {
    title = 'home';
    private expenses : Expenses;
    private resultsLength: number = 5;
    private page: number = 1;
    private pageSize: number = 5;
    private pageSizeOptions: Array<number> = [5, 10];
    private loading: boolean = false;
    @ViewChild(MatPaginator) paginator: MatPaginator;

    constructor(private expenseService: ExpenseService, private alertService: AlertService) {}

    ngOnInit() {
        this.getExpenses(this.page, this.pageSize);
    }

    private onPageChange(event: PageEvent) {
        this.getExpenses(event.pageIndex + 1, event.pageSize);
    }

    private getExpenses(page: number, pageSize: number): void {
        this.loading = true;
        this.expenseService.getAll(page, pageSize).subscribe(
        result => {
            this.expenses = result;
            this.resultsLength = result.count;
            this.loading = false;
        },
        error => {
            this.alertService.error("There was an error with fetching expenses.");
            this.loading = false;
        });
    }

    displayedColumns: string[] = ['name', 'amount', 'payee', 'added'];
}
