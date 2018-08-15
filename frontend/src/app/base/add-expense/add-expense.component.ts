import { Component, OnInit } from '@angular/core';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { ExpenseService } from '../../services/expense.service';
import { ExpenseCategoriesService } from '../../services/expense-categories.service';
import { Router } from '@angular/router';
import { ExpenseCategories } from '../../models/expense-categories.model';
import { Currency } from '../../models/currency.model';
import { LoadingComponent } from '../../shared/loading/loading.component';

@Component({
    selector: 'add-expense',
    templateUrl: './add-expense.component.html',
    styleUrls: ['./add-expense.component.css']
})
export class AddExpenseComponent implements OnInit {

    private form: FormGroup;
    private loading = false;
    private submitted = false;
    private expenseCategories: ExpenseCategories[];
    private currencies: Currency[] = [
        { value: "EUR", viewValue: "Euro - €" },
        { value: "USD", viewValue: "United States dollar - $" },
        { value: "CHF", viewValue: "Swiss franc - CHF" },
        { value: "SEK", viewValue: "Swedish krona - kr" }
    ];

    constructor(
        private formBuilder: FormBuilder,
        private expenseService: ExpenseService,
        private expenseCategoriesService: ExpenseCategoriesService,
        private router: Router
    ) { }

    ngOnInit() {
        this.form = this.formBuilder.group({
            name: [null, Validators.required],
            amount: [null, Validators.required],
            currency: [null, Validators.required],
            cash: [false],
            payee: [''],
            status: [false],
            description: [''],
            expenses_category: ['', Validators.required]
        });

        this.expenseCategoriesService.getAll()
            .subscribe(data => {
                this.expenseCategories = data;
            },
            error => {
                console.log(error);
            });
    }

    get f() { return this.form.controls; }

    onSubmit() {
        this.submitted = true;

        if (this.form.invalid) {
            return;
        }

        this.form.value.amount = this.form.value.amount * 100;
        this.loading = true;
        this.expenseService.create(this.form.value)
            .subscribe(data => {
                setTimeout(() => {
                    this.router.navigate(['home']);
                }, 500);
            },
            error => {
                console.log(error);
                this.loading = false;
            }
        );
    }
}
