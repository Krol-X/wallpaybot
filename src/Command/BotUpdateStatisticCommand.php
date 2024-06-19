<?php

namespace App\Command;

use App\Repository\PaymentRepository;
use App\Repository\PaymentStatisticRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'bot:update-statistic',
    description: 'Update payment statistic per last month',
)]
class BotUpdateStatisticCommand extends Command
{
    public function __construct(
        private readonly PaymentRepository          $paymentRepository,
        private readonly PaymentStatisticRepository $statisticsRepository
    )
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $lastMonth = new \DateTimeImmutable('first day of last month');
        $now = new \DateTimeImmutable();

        $payments = $this->paymentRepository->findPaymentsBetween($lastMonth, $now);

        $totalPayments = count($payments);
        $successfulPayments = array_filter($payments, fn($payment) => $payment->getStatus() === 'paid');
        $discountedSuccessfulPayments = array_filter($successfulPayments, fn($payment) => $payment->isDiscount());
        $failedPayments = array_filter($payments, fn($payment) => $payment->getStatus() === 'error');

        $successfulPaymentRatio = $totalPayments > 0 ? count($successfulPayments) / $totalPayments : 0;
        $discountedSuccessfulPaymentRatio = $totalPayments > 0 ? count($discountedSuccessfulPayments) / $totalPayments : 0;
        $failedPaymentRatio = $totalPayments > 0 ? count($failedPayments) / $totalPayments : 0;

        $totalRevenue = array_reduce($successfulPayments, fn($carry, $payment) => $carry + $payment->getAmount(), 0);

        $this->statisticsRepository->updateStatistics(
            $totalPayments,
            $successfulPaymentRatio,
            $discountedSuccessfulPaymentRatio,
            $failedPaymentRatio,
            $totalRevenue
        );

        $output->writeln('Статистика платежей успешно обновлена.');
        return Command::SUCCESS;
    }
}
