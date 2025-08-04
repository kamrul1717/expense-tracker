<?php

namespace App\Console\Commands;

use App\Models\Transaction;
use Illuminate\Console\Command;

class ProcessRecurringTransactions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:process-recurring-transactions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $transactions = Transaction::where('is_recurring', true)
            ->where(function($query) {
                $query->whereNull('recurrence_ends_on')
                    ->orWhere('recurrence_ends_on', '>=', now());
            })
            ->get();
        
        foreach ($transactions as $transaction) {
            $lastOccurrence = $transaction->recurrences()
                ->orderBy('occurrence_date', 'desc')
                ->first();
            
            $nextDate = $this->calculateNextDate(
                $transaction->date,
                $transaction->recurrence_frequency,
                $lastOccurrence ? $lastOccurrence->occurrence_date : null
            );
            
            if ($nextDate && $nextDate->isToday()) {
                $newTransaction = $transaction->replicate();
                $newTransaction->date = now();
                $newTransaction->is_recurring = false;
                $newTransaction->save();
                
                $transaction->recurrences()->create([
                    'occurrence_date' => now()
                ]);
            }
        }
    }
}
