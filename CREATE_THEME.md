# 🧩 Creation de themes


## 📚 Sommaire

- [⚙️ Configuration](#️-configuration)
- [📬 Publication](#-publication)

---

## ⚙️ Configuration

1. Créé un dossier avec le nom du module dans le dossier `themes`
2. Mettre à l'intérieur un fichier **config.json** 
```json
{
    "css_file": "styles.css", // Nom du fichier css de votre style
    "index": "#", // Lien externe ou chemin vers un fichier de documentation...
    "index_name": "", // Nom du bouton dans modules.php (laisser vide pour qu'il n'y est pas de bouton)
    "theme_descr": "Mon Super Theme" // description du theme
}
```
3. Mettre les fichiers nécessaires au fonctionnement du theme dans le dossier du theme

> Résultat:
```text
/-> themes
  /-> NOM_DU_THEME
    /-> config.json
    /-> styles.css
```
---


## 📬 Publication

> Pour publier un module et qu'il soit disponible via le fichier [`themes.md`](themes.md) merci de respecter quelques critères.

1. avoir un repo github public contenant:
  - Le dossier du theme complet
  - un README.md montrant des visuels du theme
    
2. Créer une issue ayant comme titre le nom de votre theme, en description un lien vers le repo du theme.