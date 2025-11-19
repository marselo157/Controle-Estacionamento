# Controle de Estacionamento Inteligente

Projeto mínimo em PHP 8+ com SQLite aplicando princípios SOLID.

### Como rodar (local)
1. Tenha PHP 8.2+ instalado.
2. Clone/extraia este repositório.
3. Crie a pasta `data` com permissão de escrita: `mkdir data && chmod 777 data`
4. Rode o script de migração para criar o banco SQLite:
   `php scripts/migrate.php`
5. Inicie o servidor embutido do PHP:
   `php -S 127.0.0.1:8000 -t public`
6. Acesse `http://127.0.0.1:8000`

### Estrutura
- `public/` - ponto de entrada web
- `src/Domain`, `src/Application`, `src/Infra` - organização modular
- `data/` - arquivo SQLite (gerado por migrate.php)
- `scripts/migrate.php` - cria DB e tabela

### Notas
- Projeto mínimo para demonstrar regras de negócio: tipos de veículo, tarifas e relatório.
- Tarifas (horas arredondadas para cima): carro R$5/h, moto R$3/h, caminhão R$10/h.
