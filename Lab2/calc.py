#
# Simple calculator created by Elena Levcenco
#
from tkinter import *
import math
import re

aux = ''
value = ''
clear = False
clicked = 0
nr = ''


# import tkMessageBox
class myCalc:
    def __init__(self, master):

        # btnClick function
        def btnclick(self):
            global aux
            global value
            global clear
            global clicked
            global nr

            # clear all
            if self == 'C':
                aux = ''
                nr = '0'
                clicked = 1

            # CE click
            elif self == 'CE':
                nr = '0'
                clicked = 1
            # % click
            elif self == '%':
                clear = True
                value = str(eval(aux[:-1]))
                nr = str(eval(value + "*" + nr + '/100')).rstrip('0').rstrip('.')
                aux += nr
                clicked = 1

            # neg click
            elif self == 'neg':
                if nr != 0:
                    nr = str(-float(nr)).rstrip('0').rstrip('.')
                clicked = 0

            # sqr click
            elif self == 'sqr':
                nr = str(float(nr) ** 2).rstrip('0').rstrip('.')
                clicked = 0

            # inverse click
            elif self == 'inverse':
                if nr == '' or nr == '0':
                    resulttxt.set('Cannot divide by zero')
                    return
                else:
                    nr = 1 / float(nr)
                clicked = 1

            # back click
            elif self == '⇐':
                if len(nr) == 1:
                    nr = '0'
                    clear = True
                else:
                    nr = nr[:-1]

            # square root
            elif self == '√':
                clear = True
                if clicked < 1:
                    aux += nr
                if not aux[len(aux) - 1].isnumeric():
                    aux = aux[:-1]
                nr = str(round(math.sqrt(eval(aux)), 2)).rstrip('0').rstrip('.')
                aux = ''


            # equal click
            elif self == '=':
                clear = True
                if clicked < 1:
                    aux += nr
                if not aux[len(aux) - 1].isnumeric():
                    aux = aux[:-1]
                nr = str(eval(aux))
                if '.' in nr:
                    nr = nr.rstrip('0').rstrip('.')
                resulttxt.set(nr)
                aux = ''

            # dot click
            elif self == '.':
                if self not in nr:
                    nr += self
            # number and operator click
            else:

                if clicked >= 1:
                    # aux += nr
                    nr = ''
                    clicked = 0
                if self.isnumeric():

                    if clear:
                        nr = ''
                    if nr != '0':
                        nr += str(self)
                elif not nr.endswith(self) and len(nr) != 0:
                    aux += nr
                    if aux[len(aux) - 1].isnumeric():
                        aux += str(self)
                        clicked += 1
                    else:
                        aux = (aux[:-1] + self)
                else:
                    nr = '0'

                clear = False

            expr.set(aux)
            resulttxt.set(nr)

        textFont = ('Helvetica', 14)
        frame = Frame(master, width=300, height=200, bg="white")
        expr = StringVar()
        resulttxt = StringVar()
        Grid.rowconfigure(master, 0, weight=1, minsize=300)
        Grid.columnconfigure(master, 0, weight=1, minsize=250)
        frame.grid(row=0, column=0, sticky=N + S + E + W)
        secentry = Label(frame, textvariable=expr, font=('Helvetica', 12), bg='white', justify='right')
        expr.set('')
        mainentry = Label(frame, textvariable=resulttxt, font=('Helvetica', 16), bg='white', justify='right')
        resulttxt.set(0)
        secentry.grid(row=0, column=0, columnspan=4, sticky=N + S + E)
        mainentry.grid(row=1, column=0, columnspan=4, sticky=N + S + E)
        # Buttons
        btnadd = Button(frame, text='+', bd=0, bg="#f5f5f5", font=textFont, command=lambda: btnclick("+"))
        btnadd.grid(row=6, column=3, sticky=N + S + E + W)
        btnsubstract = Button(frame, text='-', bd=0, bg="#f5f5f5", font=textFont, command=lambda: btnclick("-"))
        btnsubstract.grid(row=5, column=3, sticky=N + S + E + W)
        btnmultiply = Button(frame, text='x', bd=0, bg="#f5f5f5", font=textFont, command=lambda: btnclick("*"))
        btnmultiply.grid(row=4, column=3, sticky=N + S + E + W)
        btndevide = Button(frame, text='/', bd=0, bg="#f5f5f5", font=textFont, command=lambda: btnclick("/"))
        btndevide.grid(row=3, column=3, sticky=N + S + E + W)
        btnequal = Button(frame, text='=', bd=0, bg="#f5f5f5", font=textFont, command=lambda: btnclick('='))
        btnequal.grid(row=7, column=3, sticky=N + S + E + W)
        btnfr = Button(frame, text='1/x', bd=0, bg="#d6d6d6", font=textFont, command=lambda: btnclick("inverse"))
        btnfr.grid(row=2, column=3, sticky=N + S + E + W)

        btnperce = Button(frame, text='%', bd=0, bg="#d6d6d6", font=textFont, command=lambda: btnclick("%"))
        btnperce.grid(row=2, column=0, sticky=N + S + E + W)
        btnce = Button(frame, text='CE', bd=0, bg="#f5f5f5", font=textFont, command=lambda: btnclick("CE"))
        btnce.grid(row=3, column=0, sticky=N + S + E + W)
        btnseven = Button(frame, text='7', bd=0, bg="white", font=textFont, command=lambda: btnclick("7"))
        btnseven.grid(row=4, column=0, sticky=N + S + E + W)
        btnfour = Button(frame, text='4', bd=0, bg="white", font=textFont, command=lambda: btnclick("4"))
        btnfour.grid(row=5, column=0, sticky=N + S + E + W)
        btnone = Button(frame, text='1', bd=0, bg="white", font=textFont, command=lambda: btnclick("1"))
        btnone.grid(row=6, column=0, sticky=N + S + E + W)
        btnreverse = Button(frame, text='±', bd=0, bg="white", font=textFont, command=lambda: btnclick('neg'))
        btnreverse.grid(row=7, column=0, sticky=N + S + E + W)

        btnsqrt = Button(frame, text='√', bd=0, bg="#d6d6d6", font=textFont, command=lambda: btnclick("√"))
        btnsqrt.grid(row=2, column=1, sticky=N + S + E + W)
        btnc = Button(frame, text='C', bd=0, bg="#f5f5f5", font=textFont, command=lambda: btnclick('C'))
        btnc.grid(row=3, column=1, sticky=N + S + E + W)
        btneight = Button(frame, text='8', bd=0, bg="white", font=textFont, command=lambda: btnclick("8"))
        btneight.grid(row=4, column=1, sticky=N + S + E + W)
        btnfive = Button(frame, text='5', bd=0, bg="white", font=textFont, command=lambda: btnclick("5"))
        btnfive.grid(row=5, column=1, sticky=N + S + E + W)
        btntwo = Button(frame, text='2', bd=0, bg="white", font=textFont, command=lambda: btnclick("2"))
        btntwo.grid(row=6, column=1, sticky=N + S + E + W)
        btnzero = Button(frame, text='0', bd=0, bg="white", font=textFont, command=lambda: btnclick("0"))
        btnzero.grid(row=7, column=1)

        btnpow = Button(frame, text='x²', bd=0, bg="#d6d6d6", font=textFont, command=lambda: btnclick("sqr"))
        btnpow.grid(row=2, column=2, sticky=N + S + E + W)
        btnback = Button(frame, text='⇐', bd=0, bg="#f5f5f5", font=textFont, command=lambda: btnclick("⇐"))
        btnback.grid(row=3, column=2, sticky=N + S + E + W)
        btnnine = Button(frame, text='9', bd=0, bg="white", font=textFont, command=lambda: btnclick("9"))
        btnnine.grid(row=4, column=2, sticky=N + S + E + W)
        btnsix = Button(frame, text='6', bd=0, bg="white", font=textFont, command=lambda: btnclick("6"))
        btnsix.grid(row=5, column=2, sticky=N + S + E + W)
        btnthree = Button(frame, text='3', bd=0, bg="white", font=textFont, command=lambda: btnclick("3"))
        btnthree.grid(row=6, column=2, sticky=N + S + E + W)
        btndot = Button(frame, text='.', bd=0, bg="white", font=textFont, command=lambda: btnclick("."))
        btndot.grid(row=7, column=2, sticky=N + S + E + W)

        btnequal.bind("<Enter>", lambda event, h=btnequal: h.configure(bg="lightblue"))
        btnequal.bind("<Leave>", lambda event, h=btnequal: h.configure(bg="#f5f5f5"))
        for x in range(4):
            Grid.columnconfigure(frame, x, weight=1)

        for y in range(8):
            Grid.rowconfigure(frame, y, weight=1)


root = Tk()
b = myCalc(root)
root.title("Elena'sCalc")
root.mainloop()
