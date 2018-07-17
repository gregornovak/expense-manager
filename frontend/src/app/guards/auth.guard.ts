import { Injectable } from '@angular/core';
import { Router, CanActivate, ActivatedRouteSnapshot, RouterStateSnapshot } from '@angular/router';

@Injectable()
export class AuthGuard implements CanActivate {

    constructor(private router: Router) { }

    canActivate(route: ActivatedRouteSnapshot, state: RouterStateSnapshot) {
        let currentUser = localStorage.getItem('currentUser');
        let exp = localStorage.getItem('exp');

        if (currentUser && exp && this.expireChecker(exp)) {
            return true;
        }

        this.router.navigate(['/login'], { queryParams: { returnUrl: state.url }});
        return false;
    }

    private expireChecker(expiresAt: string) {
        let current : number = + new Date();
        let exp : number = Number(JSON.parse(expiresAt)) * 1000;

        return exp > current;
    }
}