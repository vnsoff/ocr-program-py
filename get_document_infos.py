import spacy

# Carregar o modelo treinado
nlp = spacy.load("models/modelo_treinado")

# Ler o texto do arquivo .txt
caminho_arquivo = "raw-text/tesseract_raw.txt"
with open(caminho_arquivo, "r", encoding="utf-8") as arquivo:
    texto = arquivo.read()

# Inicializar uma lista para armazenar os números de matrícula encontrados
numeros_cpf = []

# Processar o texto com o modelo
doc = nlp(texto)

# Extrair e armazenar apenas o número de matrícula desejado
for entidade in doc.ents:
    if entidade.label_ == "CPF":
        # Extrair apenas os números da entidade e armazenar na lista
        numero_cpf = ''.join(filter(str.isdigit, entidade.text))
        # Verificar se o número de matrícula tem o formato desejado (92.111)
        if "." in numero_cpf and len(numero_cpf.split(".")) == 2:
            numeros_cpf.append(numero_cpf)

# Se houver pelo menos um número de matrícula encontrado, imprimir o primeiro
if numeros_cpf:
    print("Número de matrícula encontrado:", numeros_cpf[0])
else:
    print("Nenhum número de matrícula encontrado.")