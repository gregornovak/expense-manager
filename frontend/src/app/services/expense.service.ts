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

    // getUserById(id: number) {
    //     return this.http.get<User>(this.baseUrl + '/' + id);
    // }

    // createUser(user: User) {
    //     return this.http.post(this.baseUrl, user);
    // }

    // updateUser(user: User) {
    //     return this.http.put(this.baseUrl + '/' + user.id, user);
    // }

    // deleteUser(id: number) {
    //     return this.http.delete(this.baseUrl + '/' + id);
    // }

}