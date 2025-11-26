# OSTRA - Plataforma de Música para Games

Este projeto contém as telas de início e cadastro da plataforma OSTRA, desenvolvidas em HTML e CSS puro.

## 📁 Estrutura do Projeto

```
ostra-project/
│
├── index.html              # Tela inicial
├── account-type.html       # Tela de seleção de tipo de conta
├── signup-viber.html       # Tela de cadastro para Viber
├── signup-producer.html    # Tela de cadastro para Produtor
├── styles.css              # Arquivo CSS principal
├── assets/                 # Pasta de imagens
│   ├── background-home.png
│   ├── modal-account-type.png
│   └── signup-viber.png
└── README.md               # Este arquivo
```

## 🎨 Páginas Criadas

### 1. **index.html** - Tela Inicial
- Header com logo OSTRA, barra de busca e botões de ação
- Imagem de fundo em tela cheia
- Design responsivo focado em desktop

### 2. **account-type.html** - Seleção de Tipo de Conta
- Modal centralizado com fundo desfocado
- Dois cards interativos:
  - **Viber**: Para usuários que querem curtir e comprar música
  - **Produtor**: Para criadores de conteúdo musical
- Efeitos hover e transições suaves

### 3. **signup-viber.html** - Cadastro para Viber
- Formulário centralizado com campos:
  - Nome de Usuário
  - E-mail
  - Senha
  - Confirmação de Senha
  - Checkbox de Termos de Uso
- Validação JavaScript básica
- Design com fundo escuro e inputs estilizados

### 4. **signup-producer.html** - Cadastro para Produtor
- Similar ao cadastro de Viber
- Texto adaptado para produtores
- Mesma estrutura e validação

## 🎯 Características do Design

### Paleta de Cores
- **Cor Principal**: `#00D9D9` (Ciano/Turquesa)
- **Fundo Escuro**: `#1a1a2e`, `#2a2a2a`
- **Texto**: `#ffffff` (Branco)
- **Inputs**: `rgba(200, 200, 220, 0.9)` com borda `#6b5dd3`

### Efeitos e Animações
- **Hover States**: Todos os botões e cards têm efeitos hover
- **Transições**: Suaves (0.3s ease)
- **Transform**: Elevação de elementos no hover
- **Box Shadow**: Brilho ciano nos elementos interativos
- **Backdrop Filter**: Blur no header e modais

### Tipografia
- **Fonte**: Segoe UI (fallback: Tahoma, Geneva, Verdana, sans-serif)
- **Logo**: Peso 700, espaçamento de 3px
- **Títulos**: Peso 600-700
- **Corpo**: Peso normal

## 🚀 Como Usar

1. Abra o arquivo `index.html` no navegador para ver a tela inicial
2. Clique em "Create Account" ou navegue para `account-type.html` para escolher o tipo de conta
3. Selecione "Viber" ou "Produtor" para ir para a respectiva tela de cadastro
4. Preencha o formulário e clique em "Cadastrar"

## 🔗 Navegação Entre Páginas

```
index.html
    ↓
account-type.html
    ↓
    ├── signup-viber.html
    └── signup-producer.html
```

## ✨ Recursos Implementados

- ✅ Design fiel aos mockups fornecidos
- ✅ CSS puro sem frameworks
- ✅ Validação de formulário com JavaScript
- ✅ Efeitos hover e transições
- ✅ SVG para ícones (logo e personagens)
- ✅ Layout responsivo para desktop
- ✅ Código limpo e bem comentado

## 🛠️ Melhorias Futuras

- [ ] Responsividade para mobile e tablet
- [ ] Integração com backend (API)
- [ ] Animações mais complexas
- [ ] Sistema de autenticação real
- [ ] Validação de senha forte
- [ ] Recuperação de senha
- [ ] Login social (Google, Facebook, etc.)

## 📝 Notas

- O projeto foi desenvolvido com foco em **desktop** conforme solicitado
- As imagens dos personagens foram criadas com SVG para manter a qualidade
- O código é modular e fácil de manter
- Todos os estilos estão centralizados no arquivo `styles.css`

---

**Desenvolvido para a plataforma OSTRA** 🎮🎵

