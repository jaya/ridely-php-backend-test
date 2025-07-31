# Ridely - Plataforma de Corridas por Táxi (PHP Backend Test - Architecture Challenge) - Arquitetura

## Notas de arquitetura do projeto
[ARCHITECTURE.md](ARCHITECTURE.md)
Este arquivo contém os diagramas e explicações da arquitetura do sistema, incluindo microsserviços, mensageria, autenticação, banco de dados e observabilidade.

## Instalar plant UML

Para gerar os diagramas da arquitetura, é necessário instalar o PlantUML e seus pré-requisitos.  
Use o script abaixo para configurar o ambiente automaticamente:
```
./scripts/plantuml/plantuml-install.sh
```
> Nota: Você deve executar este comando na raiz do projeto.

## Gerar diagramas com imagens

Após instalar o PlantUML, utilize o script abaixo para gerar imagens PNG dos diagramas `.puml` definidos no projeto.  
As imagens serão geradas automaticamente na pasta correspondente.

```
./scripts/plantuml/plantuml-create-diagrams.sh
```
> Nota: Você deve executar este comando na raiz do projeto.
 
