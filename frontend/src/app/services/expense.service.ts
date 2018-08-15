import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { apiUrl }     from '../api-url';
import { Expenses }   from '../models/expenses.model';
import { Expense }    from '../models/expense.model';
import { Observable } from 'rxjs';

@Injectable()
export class ExpenseService {

    constructor(private http: HttpClient){}

    public getAll(page?: number, limit?: number): Observable<Expenses> {
        let uri = page && limit ? `expenses?page=${page}&limit=${limit}` : 'expenses';
        return this.http.get<Expenses>(apiUrl + uri);
    }

    public getOne() {

    }

    public create(expense: Expense) {
        return this.http.post(apiUrl + 'expenses', expense);
    }

    public update() {

    }

    public delete() {

    }

}