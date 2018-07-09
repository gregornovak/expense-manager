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

        // not logged in so redirect to login page with the return url
        this.router.navigate(['/login'], { queryParams: { returnUrl: state.url }});
        return false;
    }

    private expireChecker(expiresAt: string) {
        let current = Number(String(+ new Date()).substring(0, 10));
        let exp =  new Date(JSON.parse(expiresAt));

        return exp > current;
    }
}