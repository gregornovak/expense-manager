import { NgModule }             from '@angular/core';
import { CommonModule }         from '@angular/common';
import { RouterModule, Routes } from '@angular/router';
import { LoginComponent }       from "./login/login.component";
import { BaseComponent }        from "./base/base.component";
import { HomeComponent }        from "./base/home/home.component";
import { AuthGuard }            from "./guards/auth.guard";
import { AddExpenseComponent }  from "./base/add-expense/add-expense.component";

const routes: Routes = [
    {
        path: '',
        component: BaseComponent,
        canActivate: [AuthGuard],
        children: [
            {
                path: '',
                redirectTo: 'home',
                pathMatch: 'full'
            },
            {
                path: 'home',
                component: HomeComponent,
            },
            {
                path: 'expense/new',
                component: AddExpenseComponent,
            }
        ]
    },
    { path: 'login', component: LoginComponent },
    { path: 'logout', component: LoginComponent },
    { path: '**', redirectTo: '' }
];

@NgModule({
  imports: [
    CommonModule,
    RouterModule.forRoot(routes)
  ],
  declarations: [],
  exports: [
    RouterModule
  ]
})

export class AppRoutingModule { }
