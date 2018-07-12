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
        let current = Math.floor(new Date() / 1000);
        let exp = Number(JSON.parse(expiresAt));

        return exp > current;
    }
}