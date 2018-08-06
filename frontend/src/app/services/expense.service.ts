import { Injectable } from "@angular/core";
import { HttpClient } from "@angular/common/http";
import { apiUrl }     from '../api-url';
import { Expense }   from "../models/expense.model";

@Injectable()
export class ExpenseService {

    constructor(private http: HttpClient){}

    public getAll() {
        return this.http.get<Expense[]>(apiUrl + 'expenses');
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