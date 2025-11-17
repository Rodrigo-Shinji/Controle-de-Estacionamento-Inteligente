Controle de Estacionamento Inteligente

Projeto acadêmico – PHP 8, SQLite, SOLID, Clean Code

 Sobre o Projeto

Este projeto implementa um sistema de controle de estacionamento com cadastro de entradas e saídas, cálculo automático de tarifas por tempo de permanência, relatórios de faturamento e aplicação rigorosa de princípios de SOLID, DRY, KISS, Clean Code e Object Calisthenics.

O sistema foi desenvolvido em PHP 8.2+, com banco SQLite, utilizando Composer e uma arquitetura modular dividida em Application, Domain e Infra.

Objetivos

Registrar entrada e saída de veículos.

Calcular tarifas com base no tempo de permanência e tipo de veículo.

Gerar relatórios de uso e faturamento.

Seguir princípios de engenharia de software modernos.

Criar uma aplicação simples, funcional e extensível.

Regras de Negócio
Tipos de veículo:

Carro → R$ 5,00 / hora

Moto → R$ 3,00 / hora

Caminhão → R$ 10,00 / hora

Cálculo:

Tempo cobrado em horas arredondadas para cima.

Cada entrada gera uma cobrança ao registrar saída.

Deve existir um relatório exibindo:

Total de veículos por tipo

Faturamento por tipo

Faturamento total

 Arquitetura do Projeto

Padrão utilizado: Arquitetura em camadas + princípios SOLID

index.php
src/
│
├── Application/
│   └── Service/
│       ├── CheckInService.php
│       ├── CheckOutService.php
│       ├── ParkingPriceCalculator.php
│       └── ReportService.php
│
├── Domain/
│   ├── Constants/
│   │   └── VehicleTypeConstant.php
│   ├── Entity/
│   │   ├── ParkingRecord.php
│   │   └── Vehicle.php
│   ├── Interfaces/
│   └── Repository/
│       └── SQLiteParkingRecordRepository.php
│
├── Service/
│   ├── AbstractHourlyPricingStrategy.php
│   ├── BikePricingStrategy.php
│   ├── CarPricingStrategy.php
│   ├── ParkingPriceCalculator.php
│   ├── PricingStrategyFactory.php
│   ├── SavedRatePricingStrategy.php
│   ├── TruckPricingStrategy.php
│   └── VehicleTypeValidator.php
│
├── Infra/
│   ├── Database/
│   │   └── SQLiteConnection.php
│   └── Repository/
│       └── SQLiteParkingRecordRepository.php
│
storage/
│   ├── database.sqlite
│   └── migrate.php
│
vendor/
composer.json
composer.lock

✔ SOLID aplicado

SRP: classes específicas para entidade, repositório e serviço

OCP: novos veículos e tarifas podem ser adicionados sem alterar código existente

LSP: estratégias de cálculo substituíveis

ISP: interfaces separadas para repositório e precificação

DIP: casos de uso dependem de abstrações

✔ Outros princípios

DRY: sem duplicação de lógica

KISS: código direto e limpo

Object Calisthenics aplicado durante commits e revisões

🛠 Tecnologias Utilizadas

PHP 8.2+

SQLite

Composer (autoload PSR-4)

PSR-12

HTML + Tailwind CSS (opcional)

SweetAlert2 (opcional)

▶ Como Executar o Projeto
1. Clone o repositório
git clone https://github.com/SEU-USUARIO/Controle-de-Estacionamento-Inteligente-main.git

2. Acesse a pasta
cd Controle-de-Estacionamento-Inteligente-main

3. Instale dependências com Composer
composer install

4. Gere o banco SQLite (se necessário)
php config/create_database.php

5. Inicie o servidor embutido do PHP
php -S localhost:8000 -t public

6. Acesse o sistema

Abra no navegador:

http://localhost:8000

Funcionalidades Principais

Registrar entrada de veículos

Registrar saída com cálculo de tarifa automática

Listar veículos atualmente estacionados

Relatório de faturamento por tipo

Banco SQLite integrado

Interface simples e funcional

Demonstração



Integrantes do Grupo
Guilherme Dorce the Britto
Rodrigo Shinji Yamashita
Thiago Tsuyoshi Okada Aoki

Decisões de Projeto

A arquitetura modular foi escolhida para facilitar extensões (ex: novos tipos de tarifa).

O uso de interfaces permite testes e substituição de implementações com facilidade (DIP).

SQLite foi escolhido pela simplicidade e por não exigir servidor externo.

A separação Domain/Application/Infra garante separação clara entre regras de negócio e infraestrutura.
