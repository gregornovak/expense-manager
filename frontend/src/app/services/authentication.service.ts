import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { map } from 'rxjs/operators';

@Injectable()
export class AuthenticationService {
    constructor(private http: HttpClient) { }

    login(email: string, password: string) {
        return this.http.post<any>(`http://docker.localhost:8000/api/login`, {email: email, password: password})
            .pipe(map(user => {
                if (user && user.token) {
                    let parsedToken = this.parseJwt(user.token);
                    // let exp = + new Date(parsedToken.exp);

                    localStorage.setItem('currentUser', JSON.stringify(user));
                    localStorage.setItem('exp', JSON.stringify(parsedToken.exp));
                }

                return user;
            }));
    }

    logout() {
        // remove user from local storage to log user out
        localStorage.removeItem('currentUser');
        localStorage.removeItem('exp');
    }

    private parseJwt (token : string) {
        let base64Url = token.split('.')[1];
        let base64 = base64Url.replace('-', '+').replace('_', '/');
        return JSON.parse(window.atob(base64));
    };
}
