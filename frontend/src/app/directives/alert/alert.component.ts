import { Component, OnInit, OnDestroy } from '@angular/core';
import { Subscription } from 'rxjs';
import { MatSnackBar } from '@angular/material';
import { AlertService } from '../../services/alert.service';

@Component({
    selector: 'alert',
    template: ``
})

export class AlertComponent implements OnInit, OnDestroy {
    private subscription: Subscription;
    message: any;

    constructor(private alertService: AlertService, private snackBar: MatSnackBar) { }

    ngOnInit() {
        this.subscription = this.alertService.getMessage().subscribe(message => {
            this.message = message;

            if(this.message) {
                this.snackBar.open(this.message.text, "Close");
            }
        });
    }

    ngOnDestroy() {
        this.subscription.unsubscribe();
    }
}