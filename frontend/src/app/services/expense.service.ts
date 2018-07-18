import { Injectable } from "@angular/core";
import { HttpClient } from "@angular/common/http";
import { apiUrl }     from '../api-url';
import { Expenses }   from "../models/expenses.model";

@Injectable()
export class ExpenseService {

    constructor(private http: HttpClient){}

    public getAll() {
        return this.http.get<Expenses[]>(apiUrl + 'expenses');
    }

}