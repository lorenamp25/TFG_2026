import { ComponentFixture, TestBed } from '@angular/core/testing';

import { IngredienteForm } from './ingrediente-form';

describe('IngredienteForm', () => {
  let component: IngredienteForm;
  let fixture: ComponentFixture<IngredienteForm>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [IngredienteForm]
    })
    .compileComponents();

    fixture = TestBed.createComponent(IngredienteForm);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
