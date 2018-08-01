import { Component, OnInit } from '@angular/core';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { ExpenseService } from '../../services/expense.service';
import { Router } from '@angular/router';

@Component({
    selector: 'add-expense',
    templateUrl: './add-expense.component.html',
    styleUrls: ['./add-expense.component.css']
})
export class AddExpenseComponent implements OnInit {

    private addExpenseForm: FormGroup;
    private loading = false;
    private submitted = false;

    constructor(
        private formBuilder: FormBuilder,
        private expenseService: ExpenseService,
        private router: Router
    ) { }

    ngOnInit() {
        this.addExpenseForm = this.formBuilder.group({
            name: ['', Validators.required],
            amount: ['', Validators.required],
            currency: ['', Validators.required],
            cash: [],
            payee: [],
            status: [],
            description: [],
            expenses_category: ['', Validators.required]
        });
    }

    get f() { return this.addExpenseForm.controls; }

    onSubmit() {
        this.submitted = true;

        // stop here if form is invalid
        if (this.addExpenseForm.invalid) {
            return;
        }

        this.loading = true;
        console.log(this.addExpenseForm.value);
        this.expenseService.create(this.addExpenseForm.value)
            .subscribe(data => {
                this.router.navigate(['home']);
            },
            error => {console.log(error);}
        );
        // this.authenticationService.login(this.f.email.value, this.f.password.value)
        //     .pipe(first())
        //     .subscribe(
        //         data => {
        //             this.router.navigate([this.returnUrl]);
        //         },
        //         error => {
        //             this.alertService.error(error);
        //             this.loading = false;
        //         });
    }
}
