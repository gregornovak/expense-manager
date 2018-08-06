import { Injectable } from "@angular/core";
import { HttpClient } from "@angular/common/http";
import { apiUrl }     from '../api-url';
import { ExpenseCategories }   from "../models/expense-categories.model";

@Injectable()
export class ExpenseCategoriesService {

    constructor(private http: HttpClient){}

    public getAll() {
        return this.http.get<ExpenseCategories[]>(apiUrl + 'expense-categories');
    }

    public getOne() {

    }

    public create(expense: ExpenseCategories) {
        return this.http.post(apiUrl + 'expense-categories', expense);
    }

    public update() {

    }

    public delete() {

    }
}