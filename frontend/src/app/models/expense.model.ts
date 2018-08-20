export class Expense {
    id: number;
    added: Date;
    amount: number;
    cash?: boolean;
    currency: string;
    description?: string;
    name: string;
    payee?: string;
    status?: boolean;
    updated: Date;
}