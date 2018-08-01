import { Component, OnInit } from '@angular/core';

@Component({
    selector: 'base',
    templateUrl: './base.component.html',
    styleUrls: ['./base.component.css']
})
export class BaseComponent implements OnInit{
    title = 'base';

    private firstname : String = "";
    private lastname : String = "";

    constructor() {}

    ngOnInit() {
        this.firstname = JSON.parse(localStorage.getItem('firstname'));
        this.lastname = JSON.parse(localStorage.getItem('lastname'));
    }
}
